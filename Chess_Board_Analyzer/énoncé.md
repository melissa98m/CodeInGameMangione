Les concepts suivants peuvent vous aider à résoudre ce puzzle. Découvrez-les et mettez-les en pratique pour continuer votre progression.

    Chaînes de caractères
    Boucles
    2D array
    Échecs

Énoncé
Objectif
Find the winner (W or B) for the given chess board. If there isn't a King in checkmate position output N.

You have to make several assumptions:
- The given boards are legal and are assuming the official Chess rules: https://en.wikipedia.org/wiki/Rules_of_chess
- In every board there is a winner (no draws) or the board is not terminal (the game could be continued)
- An attacked King could be saved only by moving himself to a safe square (not by using another piece from the King's team)
- White pawns are moving upwards, while black pawns are moving downwards

Example board:

........
.......k
........
........
........
......R.
.K.....R
........

In this example the white rooks (uppercase R letters) are attacking all the squares the black king (lowercase k letter) could move onto, so the black king is in checkmate position and the white (W) player wins. 