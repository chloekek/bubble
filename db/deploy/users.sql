START TRANSACTION;

CREATE COLLATION bubble.nickname (
    provider      = icu,
    locale        = 'und-u-ks-level2',
    deterministic = false
);

COMMENT ON COLLATION bubble.nickname IS '
    Case-insensitive collation for nicknames.

    While nicknames are always in ASCII, the only possible way to collate
    case-insensitively is through ICU, so that is what we do. The syntax of
    locale names is described in UTS #35 [1] and BCP 47 [2]. A short summary:

     - ‘und’ [3] means the nickname is in an unknown language.
     - ‘u’ [4] means what follows is a Unicode locale extension.
     - ‘ks-level2’ [5] means the nickname is collated according to level 2
       features, which happen to disregard case.

    [1]: https://unicode.org/reports/tr35/tr35.html
    [2]: https://tools.ietf.org/html/bcp47
    [3]: https://tools.ietf.org/html/bcp47#section-4.1
    [4]: https://unicode.org/reports/tr35/tr35.html#u_Extension
    [5]: https://unicode.org/reports/tr35/tr35-collation.html#Setting_Options
';

CREATE TABLE bubble.users (
    id uuid,
    nickname CHARACTER VARYING COLLATE bubble.nickname NOT NULL,
    password_hash CHARACTER VARYING NOT NULL,

    CONSTRAINT users_pk
        PRIMARY KEY (id),

    CONSTRAINT users_nickname_ck
        CHECK (nickname COLLATE "C" SIMILAR TO '[A-Za-z0-9]{1,20}')
);

CREATE UNIQUE INDEX users_nickname_ix
    ON bubble.users (nickname);

GRANT SELECT, INSERT, UPDATE, DELETE
    ON TABLE bubble.users
    TO bubble_application;

COMMIT WORK;
