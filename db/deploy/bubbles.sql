START TRANSACTION;

CREATE TABLE bubble.bubbles (
    id uuid,
    owner_id uuid NOT NULL,
    name VARCHAR NOT NULL,

    CONSTRAINT bubbles_pk
        PRIMARY KEY (id),

    CONSTRAINT bubbles_owner_fk
        FOREIGN KEY (owner_id)
        REFERENCES bubble.users (id)
        ON DELETE CASCADE,

    CONSTRAINT bubbles_name_ck
        CHECK (char_length(name) BETWEEN 1 AND 20)
);

COMMENT ON TABLE bubble.bubbles IS '
    A bubble is a named predicate. A post is automatically in a bubble if it
    matches the bubble’s predicate. Predicates have the syntax listed in EBNF
    in figure 1. A single conjunction is at the root, and is reified by the
    bubble itself, alongside the bubble’s metadata.

        <conjunction> := <disjunction>*
        <disjunction> := <literal>+
        <literal> := ‘not’? (<author> | <tag>)

    As you can see, predicates can filter posts by author and by tag, in a very
    fine-grained way.

    This form is called conjunctive normal form, and it is up to the
    application to coerce the user’s input into this form.
';

CREATE INDEX bubbles_owner_id_ix
    ON bubble.bubbles
    (owner_id);

CREATE TABLE bubble.bubble_disjunctions (
    id uuid,
    conjunction_id uuid NOT NULL,

    CONSTRAINT bubble_disjunctions_pk
        PRIMARY KEY (id),

    CONSTRAINT bubble_disjunctions_conjunction_fk
        FOREIGN KEY (conjunction_id)
        REFERENCES bubble.bubbles (id)
        ON DELETE CASCADE
);

CREATE INDEX bubble_disjunctions_conjunction_id_ix
    ON bubble.bubble_disjunctions
    (conjunction_id);

CREATE TABLE bubble.bubble_literals (
    id uuid,
    disjunction_id uuid NOT NULL,

    invert BOOLEAN NOT NULL,
    assert_author_id uuid,
    assert_tag VARCHAR,

    CONSTRAINT bubble_literals_pk
        PRIMARY KEY (id),

    CONSTRAINT bubble_literals_disjunction_fk
        FOREIGN KEY (disjunction_id)
        REFERENCES bubble.bubble_disjunctions (id)
        ON DELETE CASCADE,

    CONSTRAINT bubble_literals_predicate_ck
        CHECK (num_nonnulls(assert_author_id, assert_tag) = 1),

    CONSTRAINT bubble_literals_assert_author_fk
        FOREIGN KEY (assert_author_id)
        REFERENCES bubble.users (id)
        ON DELETE CASCADE
);

CREATE INDEX bubble_literals_disjunction_id_ix
    ON bubble.bubble_literals
    (disjunction_id);

CREATE INDEX bubble_literals_assert_author_id_ix
    ON bubble.bubble_literals
    (assert_author_id);

GRANT SELECT, INSERT, UPDATE, DELETE
    ON TABLE bubble.bubbles,
             bubble.bubble_disjunctions,
             bubble.bubble_literals
    TO bubble_application;

COMMIT WORK;
