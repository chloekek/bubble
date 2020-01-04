# The PHP derivations use these options to configure PHP.
# By default, many extensions are enabled, but we do not use most of them.
# By disabling them we make a huge cut in the number of dependencies.

{
    argon2 = true;
    cli = true;
    fpm = true;
    libxml2 = true;
    libzip = true;
    mbstring = true;
    mcrypt = true;
    openssl = true;
    phar = true;
    postgresql = true;
    sqlite = true;
    zip = true;
    zlib = true;

    apxs2 = false;
    bcmath = false;
    bz2 = false;
    calendar = false;
    cgi = false;
    cgoto = false;
    curl = false;
    embed = false;
    exif = false;
    ftp = false;
    gd = false;
    gettext = false;
    gmp = false;
    imap = false;
    intl = false;
    ldap = false;
    mhash = false;
    mysqli = false;
    mysqlnd = false;
    pcntl = false;
    pdo_mysql = false;
    pdo_odbc = false;
    pdo_pgsql = false;
    phpdbg = false;
    readline = false;
    soap = false;
    sockets = false;
    sodium = false;
    systemd = false;
    tidy = false;
    valgrind = false;
    xmlrpc = false;
    xsl = false;
    zts = false;
}
