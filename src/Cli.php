<?php

namespace MikeIceman\Helpers;

/**
 * Cli colored message helper
 * Used to show colored debug cli messages
 */
class Cli
{
    /** @var int Terminal width */
    public const WIDTH = 100;

    #region Constants
    public const BLACK = 'black';
    public const BLUE = 'blue';
    public const GREEN = 'green';
    public const CYAN = 'cyan';
    public const RED = 'red';
    public const PURPLE = 'purple';
    public const BROWN = 'brown';
    public const YELLOW = 'yellow';
    public const MAGENTA = 'magenta';
    public const WHITE = 'white';
    #endregion

    #region Colors definitions
    protected static array $color = [
        self::BLACK => '0;30',
        self::BLUE => '0;34',
        self::GREEN => '0;32',
        self::CYAN => '0;36',
        self::RED => '0;31',
        self::PURPLE => '0;35',
        self::BROWN => '0;33',
        self::YELLOW => '1;33',
        self::WHITE => '1;37',
    ];

    protected static array $background = [
        self::BLACK => '40',
        self::RED => '41',
        self::GREEN => '42',
        self::YELLOW => '43',
        self::BLUE => '44;1',
        self::MAGENTA => '45',
        self::CYAN => '46',
    ];
    #endregion

    /**
     * @param mixed $message
     * @param string|null $title
     */
    public function prompt(mixed $message, ?string $title = 'Debug'): void
    {
        $this->shout($this->wrap($message, $title));
    }

    /**
     * @param mixed $message
     * @param string|null $title
     * @param bool $centered
     */
    public function info(mixed $message, ?string $title = 'Info', bool $centered = true): void
    {
        $this->shout($this->wrap($message, $title, $centered), self::BLACK, self::BLUE);
    }

    /**
     * @param mixed $message
     * @param string|null $title
     * @param bool $centered
     */
    public function success(mixed $message, ?string $title = 'Success', bool $centered = true): void
    {
        $this->shout($this->wrap($message, $title, $centered), self::BLACK, self::GREEN);
    }

    /**
     * @param mixed $message
     * @param string|null $title
     * @param bool $centered
     */
    public function warning(mixed $message, ?string $title = 'Warning', bool $centered = true): void
    {
        $this->shout($this->wrap($message, $title, $centered), self::BLACK, self::YELLOW);
    }

    /**
     * @param mixed $message
     * @param string|null $title
     * @param bool $centered
     */
    public function error(mixed $message, ?string $title = 'Error', bool $centered = false): void
    {
        $this->shout($this->wrap($message, $title, $centered), self::BLACK, self::RED);
    }

    /**
     * @param mixed $message
     * @param string|null $title
     * @param bool $centered
     *
     * @return array
     */
    public function wrap(mixed $message, ?string $title = null, bool $centered = false): array
    {
        $result = [];
        if (!empty($title)) {
            $result[] =
                '+' .
                str_pad(' ' . $title . ' ', self::WIDTH - 2, '-', STR_PAD_BOTH) .
                '+'
            ;
        }

        $strings = [];

        if (is_string($message)) {
            $strings = explode("\n", wordwrap($message, self::WIDTH - 4, "\n", true));
        } elseif (is_array($message)) {
            $strings = $message;
        } elseif (is_object($message)) {
            $strings = [serialize($message)];
        }

        foreach ($strings as $string) {
            $result[] = '| ' . str_pad(
                $string,
                self::WIDTH - 4,
                ' ',
                $centered ? STR_PAD_BOTH : STR_PAD_RIGHT
            ) . ' |';
        }

        $result[] = '+' . str_repeat('-', self::WIDTH - 2) . '+';

        return $result;
    }

    /**
     * @param string $text
     * @param string|null $color
     * @param string|null $background
     *
     * @return string
     */
    public function colorize(string $text, ?string $color = null, ?string $background = null): string
    {
        $colored = '';

        if (!empty($color) && isset(static::$color[$color])) {
            $colored .= "\033[" . static::$color[$color] . 'm';
        }

        if (!empty($background) && isset(static::$background[$background])) {
            $colored .= "\033[" . static::$background[$background] . 'm';
        }

        return $colored . $text . "\033[0m";
    }

    /**
     * Progress bar
     *
     * @param int $done Processed records
     * @param int $total Total records
     * @param string|null $info Additional information
     * @param bool $details Show detailed operation progress
     * @param bool $start Timestamp when operation starts (how estimate if specified and details enabled)
     * @param int $width Progress bar width
     */
    public function progress(
        int $done,
        int $total,
        ?string $info,
        bool $details = false,
        bool $start = false,
        int $width = 50
    ): void {
        /**
         * Allow output only in cli
         */
        if (PHP_SAPI !== 'cli') {
            return;
        }

        $percent = round(($done * 100) / $total);
        $bar = round(($width * $percent) / 100);
        $estimate = str_repeat(' ', 10);
        $counter = str_repeat('  ', strlen((string)$total) + 3);

        if ($start && $done < $total) {
            $completed = $done / $total;
            $elapsed = time() - $start;
            $estimate = '[' . gmdate('H:i:s', ($elapsed / $completed) - $elapsed) . ']';
        }

        if ($details && $done < $total) {
            $counter = sprintf('(%s/%s)', $done, $total);
        }

        echo sprintf(
            "%s%%[%s>%s] %s %s %s\r",
            str_pad($percent, 3, ' ', STR_PAD_LEFT),
            str_repeat('=', $bar),
            str_repeat(' ', $width - $bar),
            $info,
            $counter,
            $estimate
        );

        // Operation completed
        if ($done >= $total) {
            echo PHP_EOL;
        }
    }

    /**
     * @param array $strings
     * @param string|null $color
     * @param string|null $background
     */
    private function shout(array $strings, ?string $color = null, ?string $background = null): void
    {
        /**
         * Allow output only in cli
         */
        if (PHP_SAPI !== 'cli') {
            return;
        }

        echo PHP_EOL;
        foreach ($strings as $string) {
            echo $this->colorize($string, $color, $background) . PHP_EOL;
        }
        echo PHP_EOL;
    }
}
