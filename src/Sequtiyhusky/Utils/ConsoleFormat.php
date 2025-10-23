<?php

namespace Sequtiyhusky\Fania\Utils;

class ConsoleFormat
{
    /** @var string Reset Ascii */
    public  const NORMAL = "\e[0m";

    /** @var string Reset Ascii */
    public  const RESET = "\e[0m";

    /** @var string Bold attributes */
    public  const BOLD = "\e[1m";

    /** @var string Undo bold attributes */
    public  const UN_BOLD = "\e[21m";

    /** @var string Dim attributes */
    public  const DIM = "\e[2m";

    /** @var string Undo dim attributes */
    public  const UN_DIM = "\e[22m";

    /** @var string Underlined attributes */
    public  const UNDERLINED = "\e[4m";

    /** @var string Undo underlined attributes */
    public  const UN_UNDERLINED = "\e[24m";

    /** @var string Blink attributes */
    public  const BLINK = "\e[5m";

    /** @var string Undo blink attributes */
    public  const UN_BLINK = "\e[25m";

    /** @var string Reverse attributes */
    public  const REVERSE = "\e[7m";

    /** @var string Undo reverse attributes */
    public  const UN_REVERSE = "\e[27m";

    /** @var string Hidden attributes */
    public  const HIDDEN = "\e[8m";

    /** @var string Undo hidden attributes */
    public  const UN_HIDDEN = "\e[28m";

    /** @var string Foreground color: Black */
    public  const BLACK = "\033[0;30m";

    /** @var string Foreground color: Dark Gray */
    public  const DARK_GRAY = "\033[1;30m";

    /** @var string Foreground color: Red */
    public  const RED = "\033[0;31m";

    /** @var string Foreground color: Light Red */
    public  const LIGHT_RED = "\033[1;31m";

    /** @var string Foreground color: Green */
    public  const GREEN = "\033[0;32m";

    /** @var string Foreground color: Light Green */
    public  const LIGHT_GREEN = "\033[1;32m";

    /** @var string Foreground color: Brown */
    public  const BROWN = "\033[0;33m";

    /** @var string Foreground color: Yellow (alias for Brown) */
    public  const YELLOW = "\033[0;33m";

    /** @var string Foreground color: Blue */
    public  const BLUE = "\033[0;34m";

    /** @var string Foreground color: Light Blue */
    public  const LIGHT_BLUE = "\033[1;34m";

    /** @var string Foreground color: Magenta */
    public  const MAGENTA = "\033[0;35m";

    /** @var string Foreground color: Purple (dim magenta) */
    public  const PURPLE = "\033[2;35m";

    /** @var string Foreground color: Light Magenta */
    public  const LIGHT_MAGENTA = "\033[1;35m";

    /** @var string Foreground color: Light Purple (alias for Light Magenta) */
    public  const LIGHT_PURPLE = "\033[1;35m";

    /** @var string Foreground color: Cyan */
    public  const CYAN = "\033[0;36m";

    /** @var string Foreground color: Light Cyan */
    public  const LIGHT_CYAN = "\033[1;36m";

    /** @var string Foreground color: Light Gray */
    public  const LIGHT_GRAY = "\033[2;37m";

    /** @var string Foreground color: Bold White */
    public  const BOLD_WHITE = "\033[1;38m";

    /** @var string Foreground color: Light White (alias for Bold White) */
    public  const LIGHT_WHITE = "\033[1;38m";

    /** @var string Foreground color: White */
    public  const WHITE = "\033[0;38m";

    /** @var string Foreground color: Default (reset to terminal's default foreground) */
    public  const FG_DEFAULT = "\033[39m";

    /** @var string Foreground color: Gray (alternative to Light Gray) */
    public  const GRAY = "\033[0;90m";

    /** @var string Foreground color: Light Red (alternative version) */
    public  const LIGHT_RED_ALT = "\033[91m";

    /** @var string Foreground color: Light Green (alternative version) */
    public  const LIGHT_GREEN_ALT = "\033[92m";

    /** @var string Foreground color: Light Yellow (alternative version) */
    public  const LIGHT_YELLOW_ALT = "\033[93m";

    /** @var string Foreground color: Light Yellow */
    public  const LIGHT_YELLOW = "\033[1;93m";

    /** @var string Foreground color: Light Blue (alternative version) */
    public  const LIGHT_BLUE_ALT = "\033[94m";

    /** @var string Foreground color: Light Magenta (alternative version) */
    public  const LIGHT_MAGENTA_ALT = "\033[95m";

    /** @var string Foreground color: Light Cyan (alternative version) */
    public  const LIGHT_CYAN_ALT = "\033[96m";

    /** @var string Foreground color: Light White (alternative version) */
    public  const LIGHT_WHITE_ALT = "\033[97m";

    /** @var string Background color: Black */
    public  const BG_BLACK = "\033[40m";

    /** @var string Background color: Red */
    public  const BG_RED = "\033[41m";

    /** @var string Background color: Green */
    public  const BG_GREEN = "\033[42m";

    /** @var string Background color: Yellow */
    public  const BG_YELLOW = "\033[43m";

    /** @var string Background color: Blue */
    public  const BG_BLUE = "\033[44m";

    /** @var string Background color: Magenta */
    public  const BG_MAGENTA = "\033[45m";

    /** @var string Background color: Cyan */
    public  const BG_CYAN = "\033[46m";

    /** @var string Background color: Light Gray */
    public  const BG_LIGHT_GRAY = "\033[47m";

    /** @var string Background color: Default (reset to terminal's default background) */
    public  const BG_DEFAULT = "\033[49m";

    /** @var string Background color: Dark Gray */
    public  const BG_DARK_GRAY = "\e[100m";

    /** @var string Background color: Light Red */
    public  const BG_LIGHT_RED = "\e[101m";

    /** @var string Background color: Light Green */
    public  const BG_LIGHT_GREEN = "\e[102m";

    /** @var string Background color: Light Yellow */
    public  const BG_LIGHT_YELLOW = "\e[103m";

    /** @var string Background color: Light Blue */
    public  const BG_LIGHT_BLUE = "\e[104m";

    /** @var string Background color: Light Magenta */
    public  const BG_LIGHT_MAGENTA = "\e[105m";

    /** @var string Background color: Light Cyan */
    public  const BG_LIGHT_CYAN = "\e[106m";

    /** @var string Background color: White */
    public  const BG_WHITE = "\e[107m";

    // private so external code cannot modify
    private static ?self $instance = null;

    public function __construct()
    {
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function print()
    {
        echo "a";
    }

    private function rgbToXterm256(int $r, int $g, int $b)
    {
        $ri = (int) round($r / 255 * 5);
        $gi = (int) round($g / 255 * 5);
        $bi = (int) round($b / 255 * 5);
        return 16 + (36 * $ri) + (6 * $gi) + $bi;
    }


    private function hexToRgb(string $hex): array
    {
        $h = ltrim($hex, '#');
        if (strlen($h) === 3) {
            $h = $h[0] . $h[0] . $h[1] . $h[1] . $h[2] . $h[2];
        }
        return [
            hexdec(substr($h, 0, 2)),
            hexdec(substr($h, 2, 2)),
            hexdec(substr($h, 4, 2)),
        ];
    }

    public  function gradientText(string $text, string $startHex, string $endHex, bool $background = false): string
    {
        $start = $this->hexToRgb($startHex);
        $end   = $this->hexToRgb($endHex);
        $len = mb_strlen($text, 'UTF-8');
        if ($len === 0) return '';

        $useTrue = true;
        $out = '';

        for ($i = 0; $i < $len; $i++) {
            $ch = mb_substr($text, $i, 1, 'UTF-8');
            $t = ($len === 1) ? 0 : ($i / ($len - 1)); // position 0..1
            $r = (int) round($start[0] + ($end[0] - $start[0]) * $t);
            $g = (int) round($start[1] + ($end[1] - $start[1]) * $t);
            $b = (int) round($start[2] + ($end[2] - $start[2]) * $t);

            if ($useTrue) {
                $code = $background ? "48;2;{$r};{$g};{$b}" : "38;2;{$r};{$g};{$b}";
                $out .= "\033[{$code}m{$ch}";
            } else {
                $idx = $this->rgbToXterm256($r, $g, $b);
                $code = $background ? "48;5;{$idx}" : "38;5;{$idx}";
                $out .= "\033[{$code}m{$ch}";
            }
        }

        $out .= "\033[0m"; // reset
        return $out;
    }
}
