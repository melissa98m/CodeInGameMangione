<?php

class ChessGame
{
    private array $board;

    /**
     * @param array $board
     */
    public function __construct(array $board)
    {
        $this->board = $board;
    }

    /**
     * @param int $row
     * @param int $col
     * @return bool
     */
    private function isInBounds(int $row, int $col): bool
    {
        return $row >= 0 && $row < 8 && $col >= 0 && $col < 8;
    }

    /**
     * @param array $kingPosition
     * @param bool $isWhiteKing
     * @return bool
     */
    private function isKingInCheck(array $kingPosition, bool $isWhiteKing): bool
    {
        [$kingRow, $kingCol] = $kingPosition;

        $directions = [
            [-1, 0], [1, 0], [0, -1], [0, 1],  // rook/Queen
            [-1, -1], [-1, 1], [1, -1], [1, 1]  // bishop/Queen
        ];

        foreach ($directions as [$dr, $dc]) {
            $r = $kingRow + $dr;
            $c = $kingCol + $dc;

            while ($this->isInBounds($r, $c)) {
                $piece = $this->board[$r][$c];
                if ($piece != '.') {
                    if (($isWhiteKing && ctype_lower($piece)) || (!$isWhiteKing && ctype_upper($piece))) {
                        if (($piece === 'R' || $piece === 'r') && ($dr === 0 || $dc === 0)) return true;
                        if (($piece === 'B' || $piece === 'b') && abs($dr) === abs($dc)) return true;
                        if ($piece === 'Q' || $piece === 'q') return true;
                    }
                    break;
                }
                $r += $dr;
                $c += $dc;
            }
        }

        $knightMoves = [
            [-2, -1], [-2, 1], [2, -1], [2, 1],
            [-1, -2], [-1, 2], [1, -2], [1, 2]
        ];

        foreach ($knightMoves as [$dr, $dc]) {
            $r = $kingRow + $dr;
            $c = $kingCol + $dc;
            if ($this->isInBounds($r, $c)) {
                $piece = $this->board[$r][$c];
                if (($isWhiteKing && $piece === 'n') || (!$isWhiteKing && $piece === 'N')) {
                    return true;
                }
            }
        }

        $pawnRowOffset = $isWhiteKing ? -1 : 1;
        $pawnCols = [$kingCol - 1, $kingCol + 1];
        foreach ($pawnCols as $c) {
            $r = $kingRow + $pawnRowOffset;
            if ($this->isInBounds($r, $c)) {
                $piece = $this->board[$r][$c];
                if (($isWhiteKing && $piece === 'p') || (!$isWhiteKing && $piece === 'P')) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param array $kingPosition
     * @param bool $isWhiteKing
     * @return bool
     */
    private function isKingMated(array $kingPosition, bool $isWhiteKing): bool
    {
        [$kingRow, $kingCol] = $kingPosition;

        $kingMoves = [
            [0, 0],
            [-1, 0], [1, 0], [0, -1], [0, 1],
            [-1, -1], [-1, 1], [1, -1], [1, 1]
        ];

        foreach ($kingMoves as [$dr, $dc]) {
            $newRow = $kingRow + $dr;
            $newCol = $kingCol + $dc;

            if ($this->isInBounds($newRow, $newCol)) {
                $originalSquare = $this->board[$newRow][$newCol];
                if ($originalSquare === '.' || (($isWhiteKing && ctype_lower($originalSquare)) || (!$isWhiteKing && ctype_upper($originalSquare)))) {
                    $this->board[$newRow][$newCol] = $isWhiteKing ? 'K' : 'k';
                    $this->board[$kingRow][$kingCol] = '.';

                    $safe = !$this->isKingInCheck([$newRow, $newCol], $isWhiteKing);

                    $this->board[$newRow][$newCol] = $originalSquare;
                    $this->board[$kingRow][$kingCol] = $isWhiteKing ? 'K' : 'k';

                    if ($safe) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * @return string
     */
    public function checkForCheckmate(): string
    {
        $whiteKing = $blackKing = null;

        for ($r = 0; $r < 8; $r++) {
            for ($c = 0; $c < 8; $c++) {
                if ($this->board[$r][$c] === 'K') $whiteKing = [$r, $c];
                if ($this->board[$r][$c] === 'k') $blackKing = [$r, $c];
            }
        }

        if ($this->isKingInCheck($whiteKing, true) && $this->isKingMated($whiteKing, true)) {
            return 'B';
        }

        if ($this->isKingInCheck($blackKing, false) && $this->isKingMated($blackKing, false)) {
            return 'W';
        }

        return 'N';
    }
}

//Replace the table with one of the examples in test.txt
$board = [
    "........",
    ".......k",
    "........",
    "........",
    "........",
    "......R.",
    ".K.....R",
    "........"
];

$game = new ChessGame($board);
echo $game->checkForCheckmate();
