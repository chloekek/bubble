START TRANSACTION;

CREATE COLLATION bubble.tag (
    provider      = icu,
    locale        = 'und-u-ks-level2',
    deterministic = false
);

COMMENT ON COLLATION bubble.tag IS '
    Case-insensitive collation for tags.

    See the comment on bubble.nickname for information about the locale.
';

CREATE FUNCTION bubble.extract_tags(CHARACTER VARYING)
    RETURNS CHARACTER VARYING[]
    AS $$
        SELECT
            CAST(
                COALESCE(array_agg(DISTINCT match[1] COLLATE bubble.tag), '{}')
                AS CHARACTER VARYING[]
            )
        FROM
            regexp_matches($1, '#([A-Za-z0-9]{1,20})', 'g') AS match
    $$
    LANGUAGE sql
    IMMUTABLE
    RETURNS NULL ON NULL INPUT
    PARALLEL SAFE;

COMMENT ON FUNCTION bubble.extract_tags IS '
    Extract the tags from a post’s body.
';

CREATE TABLE bubble.posts (
    id uuid,
    published TIMESTAMP WITH TIME ZONE,
    author_id uuid NOT NULL,
    body CHARACTER VARYING NOT NULL,
    tags CHARACTER VARYING[] COLLATE bubble.tag NOT NULL
        GENERATED ALWAYS AS (bubble.extract_tags(body)) STORED,

    CONSTRAINT posts_pk
        PRIMARY KEY (id),

    CONSTRAINT posts_author_fk
        FOREIGN KEY (author_id)
        REFERENCES bubble.users (id)
        ON DELETE CASCADE,

    CONSTRAINT posts_body_ck
        CHECK (char_length(body) BETWEEN 1 AND 1000000),

    CONSTRAINT posts_tags_ck
        CHECK (cardinality(tags) BETWEEN 1 AND 3)
);

CREATE INDEX posts_published_ix
    ON bubble.posts
    (published);

CREATE INDEX posts_author_id_ix
    ON bubble.posts
    (author_id);

CREATE INDEX posts_tags_ix
    ON bubble.posts
    USING gin
    (tags);

GRANT EXECUTE
    ON FUNCTION bubble.extract_tags
    TO bubble_application;

GRANT SELECT, INSERT, UPDATE, DELETE
    ON TABLE bubble.posts
    TO bubble_application;

COMMIT WORK;
