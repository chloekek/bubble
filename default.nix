{ pkgs ? import ./nix/pkgs.nix {}
, target ? null }:
let
    targets = rec {
        inherit (pkgs)
            bash
            coreutils
            doxygen
            gnused
            graphviz
            hivemind
            nginx
            nix
            php74
            postgresql_12
            rsync
            sassc
            sqitchPg;
        inherit (pkgs.php74Packages)
            composer;
    };
in
    if target == null
        then targets
        else targets."${target}"
