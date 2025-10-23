<?php

namespace Sequtiyhusky\Fania\Helpers;

use Sequtiyhusky\Fania\Utils\ConsoleFormat;
use Sequtiyhusky\Fania\Helpers\ChatManager;

class DialogEngine
{
    private $data;
    private $nodes = [];
    private $sceneMap = [];
    private $currentNode;
    private $playerState;

    /**
     * When true, the engine has displayed a node and is waiting for input.
     * Prevents commands like 'status' from accidentally advancing the dialog.
     */
    private $awaitingResponse = false;
    private $chat;

    /**
     * True when start() has been invoked (to avoid double-display)
     */
    private $started = false;

    /**
     * Debug mode prints received raw input info to help diagnose stuck inputs.
     * Set via setDebug(true) during testing.
     */
    private $debug = false;

    private $narratorContexts = [];

    public function __construct(ChatManager $chat)
    {
        $this->chat = $chat;
        $this->loadGameData();
        $this->playerState = [
            'reputation' => 0,
            'supplies' => 0,
            'intel' => 0,
            'bravery' => 0,
            'money' => 0,
            'morality' => 0,
            'alternate_history' => false,
            'current_node' => null,
        ];


        if ($this->debug) {
            $this->chat->reply("[DEBUG] DialogEngine::__construct invoked. Backtrace (top 6):");
            $bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 6);
            foreach ($bt as $i => $frame) {
                $file = $frame['file'] ?? '(internal)';
                $line = $frame['line'] ?? '?';
                $func = ($frame['class'] ?? '') . ($frame['type'] ?? '') . ($frame['function'] ?? '');
                $this->chat->reply("[DEBUG]  #{$i} {$func} @ {$file}:{$line}");
            }
        }
    }

    public function loadNarratorContexts($filePath)
    {
        if (file_exists($filePath)) {
            $json = json_decode(file_get_contents($filePath), true);
            foreach ($json as $entry) {
                $this->narratorContexts[$entry['scene_id']] = $entry['narrator'];
            }
        }
    }


    private function loadGameData()
    {
        $jsonPath = __DIR__ . "/../../../Database/Game/diponegoro_s.json";
        if (!file_exists($jsonPath)) {
            throw new \RuntimeException("Game file not found: $jsonPath");
        }

        $raw = file_get_contents($jsonPath);
        $this->data = json_decode($raw, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException("Failed to parse JSON: " . json_last_error_msg());
        }

        // Build indexes
        foreach ($this->data['scenes'] as $scene) {
            $this->sceneMap[$scene['id']] = $scene;
            foreach ($scene['dialogue_nodes'] as $node) {
                $this->nodes[$node['id']] = $node;
            }
        }

        // Set initial node
        $startScene = $this->data['scenes'][0] ?? null;
        if (!$startScene || empty($startScene['dialogue_nodes'])) {
            throw new \RuntimeException("No valid starting scene found");
        }

        $this->currentNode = $startScene['dialogue_nodes'][0];
        // store current node id so it can be saved/restored
        if (isset($this->currentNode['id'])) {
            $this->playerState['current_node'] = $this->currentNode['id'];
        }
    }

    /**
     * Start the dialog session (display the current node and await input).
     * Call this after instantiating the engine.
     */
    public function start()
    {
        if ($this->started) {
            return;
        }
        $this->started = true;
        $this->displayCurrentNode();
        $this->awaitingResponse = true;
    }

    /**
     * Resume from previously set playerState (if current_node present),
     * then display the current node.
     */
    public function resume()
    {
        // if a current_node exists in state and the node is known, move there
        if (!empty($this->playerState['current_node']) && isset($this->nodes[$this->playerState['current_node']])) {
            $this->currentNode = $this->nodes[$this->playerState['current_node']];
        }
        $this->displayCurrentNode();
        $this->awaitingResponse = true;
        $this->started = true;

    }

    public function showStatus()
    {
        $this->chat->reply("\n-- STATUS --");
        foreach ($this->playerState as $k => $v) {
            if (is_bool($v)) $v = $v ? 'true' : 'false';
            $this->chat->reply(ucfirst($k) . ": " . $v);
        }
        $this->chat->reply("-- END STATUS --\n");
        // keep awaitingResponse true so the dialog doesn't auto-advance
        $this->awaitingResponse = true;
    }

    /**
     * Enable or disable debug output.
     */
    public function setDebug(bool $on = true)
    {
        $this->debug = (bool)$on;
    }

    public function isDebug(): bool
    {
        return $this->debug;
    }

    public function processInput($input)
    {   
        $currentNodeId = $this->currentNode['id'] ?? '(null)';
        if ($this->debug) {
            $this->chat->reply("[DEBUG] current_node_id={$currentNodeId}");
            $this->chat->reply("[DEBUG] node_text_snippet=" . substr($this->currentNode['text'] ?? '', 0, 80));
        }

        // enumerate choices that engine sees
        $choices = $this->currentNode['choices'] ?? [];
        if ($this->debug) {
            $this->chat->reply("[DEBUG] choices_count=" . count($choices));
            foreach ($choices as $i => $ch) {
                $idx = $i + 1;
                $lbl = $ch['label'] ?? '(no-label)';
                $nxt = $ch['next'] ?? '(no-next)';
                $this->chat->reply("[DEBUG]   {$idx}. label='{$lbl}' -> next='{$nxt}'");
            }
        }

        // (then continue with existing code; keep the subsequent block that handles empty-choices etc.)

        if (empty($this->currentNode)) {
            return false;
        }

        // normalize input early
        $input = (string)$input;
        // remove typical ANSI escape sequences (colors, etc.)
        $input = preg_replace('/\x1B\[[0-9;]*[A-Za-z]/', '', $input);
        // remove other invisible control chars except newline/tab/space (safe-trim)
        $input = preg_replace('/[^\P{C}\n\r\t ]+/u', '', $input);
        $trimmed = trim($input);
        $lower = strtolower($trimmed);

        // debug output to help diagnose stuck cases
        if ($this->debug) {
            $this->chat->reply("[DEBUG] awaiting={$this->awaitingResponse}, raw_hex=" . bin2hex($input));
            $this->chat->reply("[DEBUG] trimmed='" . $trimmed . "'");
        }

        // Handle special commands without advancing dialog
        if ($lower === 'status') {
            $this->showStatus();
            // do not change awaitingResponse; keep waiting for player's real input
            return true;
        }

        if ($lower === 'help') {
            $this->chat->reply("Perintah khusus: status, help, quit");
            $this->awaitingResponse = true;
            return true;
        }

        if (in_array($lower, ['quit', 'q'])) {
            return false;
        }

        $choices = $this->currentNode['choices'] ?? [];

        // If there are no choices for this node, treat Enter (empty input) as "advance"
        if (empty($choices)) {
            if ($trimmed === "") {
                // advance only if there's a next node
                if (isset($this->currentNode['next'])) {
                    if ($this->moveToNode($this->currentNode['next'])) {
                        $this->displayCurrentNode();
                        return true;
                    }
                }
                // no next -> end
                $this->chat->reply("Tidak ada lagi kelanjutan cerita.");
                return false;
            }
            // re-display current node to remind available action
            $this->chat->reply("Tidak ada pilihan numerik di node ini. Tekan Enter untuk lanjut atau ketik 'status'/'help'.");
            $this->displayCurrentNode();
            return true;
        }

        // If there's choices, accept numeric selection regardless of awaitingResponse state.
        // This avoids cases where awaitingResponse wasn't set properly.
        if (preg_match('/^\d+$/', $trimmed)) {
            $choiceIndex = (int)$trimmed - 1;
            if (!isset($choices[$choiceIndex])) {
                $this->chat->reply("Pilihan tidak valid (terima angka 1.." . count($choices) . "). Coba lagi.");
                $this->displayCurrentNode();
                return true;
            }

            $chosen = $choices[$choiceIndex];

            if ($this->debug) {
                $this->chat->reply("[DEBUG] selected_choice_index=" . ($choiceIndex + 1));
                $this->chat->reply("[DEBUG] chosen_next_raw='" . ($chosen['next'] ?? '(none)') . "'");
                $testNext = $chosen['next'] ?? null;
                $normalizedTest = $testNext;
                if (preg_match('/^(scene-[a-z0-9\-]+)-start$/i', (string)$testNext, $m)) {
                    $normalizedTest = $m[1];
                }
                $existsNode = isset($this->nodes[$normalizedTest]) ? 'node' : (isset($this->sceneMap[$normalizedTest]) ? 'scene' : 'missing');
                $this->chat->reply("[DEBUG] normalized_next='{$normalizedTest}', exists={$existsNode}");
            }

            // Apply effects
            if (isset($chosen['effects']) && is_array($chosen['effects'])) {
                foreach ($chosen['effects'] as $k => $v) {
                    if (!array_key_exists($k, $this->playerState)) {
                        $this->playerState[$k] = $v;
                        continue;
                    }
                    if (is_bool($this->playerState[$k])) {
                        $this->playerState[$k] = (bool)$v;
                    } elseif (is_numeric($v) && is_numeric($this->playerState[$k])) {
                        $this->playerState[$k] += $v;
                    } else {
                        $this->playerState[$k] = $v;
                    }
                }
            }

            $next = $chosen['next'] ?? null;
            if (!$next) {
                $this->chat->reply("Cerita berakhir di sini.");
                return false;
            }

            if ($this->moveToNode($next)) {
                $this->displayCurrentNode();
                return true;
            }
            return false;
        }

        // Some players might type the label instead of number — attempt loose matching
        foreach ($choices as $idx => $c) {
            $label = strtolower($c['label'] ?? '');
            if ($label !== '' && strpos($label, $lower) !== false) {
                // found a fuzzy match on label
                if ($this->debug) {
                    $this->chat->reply("[DEBUG] matched by label to choice " . ($idx + 1));
                }
                // simulate choice selection
                return $this->processInput((string)($idx + 1));
            }
        }

        // If we got here, input is not understood
        $this->chat->reply("Input tidak dimengerti ('{$trimmed}'). Masukkan nomor pilihan (1.." . count($choices) . ") atau perintah ('status','help','quit').");
        $this->displayCurrentNode();
        return true;
    }

    private function moveToNode($next)
    {
        // normalize next for 'scene-xxx-start' variants
        $normalized = $next;
        if (!isset($this->sceneMap[$normalized]) && preg_match('/^(scene-[a-z0-9\-]+)-start$/i', $next, $m)) {
            $normalized = $m[1];
        }

        // Check if it's a scene transition
        if (strpos($normalized, 'scene-') === 0) {
            if (!isset($this->sceneMap[$normalized])) {
                $this->chat->reply("Scene tidak ditemukan: $normalized");
                return false;
            }
            $scene = $this->sceneMap[$normalized];
            $this->currentNode = $scene['dialogue_nodes'][0] ?? null;
            if ($this->currentNode && isset($this->currentNode['id'])) {
                $this->playerState['current_node'] = $this->currentNode['id'];
            }
            if ($this->debug) {
                $this->chat->reply("[DEBUG] moved_to_scene='{$normalized}', node_id='" . ($this->currentNode['id'] ?? '(null)') . "'");
            }
            return true;
        }

        // Check if it's END
        if ($normalized === 'END') {
            $this->chat->reply("-- TAMAT --");
            return false;
        }

        // Move to specific node
        if (isset($this->nodes[$normalized])) {
            $this->currentNode = $this->nodes[$normalized];
            if (isset($this->currentNode['id'])) {
                $this->playerState['current_node'] = $this->currentNode['id'];
            }
            if ($this->debug) {
                $this->chat->reply("[DEBUG] moved_to_node_id='" . $this->currentNode['id'] . "'");
            }
            return true;
        }

        $this->chat->reply("Node tujuan tidak ditemukan: $normalized");
        return false;
    }


    public function displayCurrentNode()
    {
        if (!$this->currentNode) {
            return;
        }

        // Ensure we mark awaitingResponse false before printing to avoid reentrancy issues.
        // We'll set it true at the end when ready for input.
        $this->awaitingResponse = false;

        $nodeId = $this->currentNode['id'] ?? '(no-id)';
        // simpan node yang sedang ditampilkan ke playerState agar selalu sinkron
        $this->playerState['current_node'] = ($this->currentNode['id'] ?? null);
        if ($this->debug) {
            $this->chat->reply("[DEBUG] displayCurrentNode -> node_id='{$nodeId}'");
        }
        $speaker = $this->currentNode['speaker'] ?? 'Narrator';
        $this->chat->reply("\n" . ConsoleFormat::CYAN . $speaker . " [" . $nodeId . "]:" . ConsoleFormat::RESET);

        $this->chat->reply($this->currentNode['text'] . "\n");

        $choices = $this->currentNode['choices'] ?? [];
        if (empty($choices)) {
            $this->chat->reply("(Tekan Enter untuk lanjut atau ketik 'quit' untuk keluar)");
            // waiting for Enter
            $this->awaitingResponse = true;
            return;
        }

        foreach ($choices as $idx => $c) {
            $num = $idx + 1;
            $label = $c['label'] ?? '(tidak ada label)';
            $this->chat->reply(ConsoleFormat::YELLOW . "  [$num]" . ConsoleFormat::RESET . " $label");
        }

        $this->chat->reply("\n(Tip: ketik 'status' untuk status, 'help' untuk bantuan, 'quit' untuk keluar)");
        // mark that we're now waiting for player input
        $this->awaitingResponse = true;

        // Extra debug: print the mapping of choices -> next for clarity
        if ($this->debug) {
            foreach ($choices as $i => $ch) {
                $idx = $i + 1;
                $nxt = $ch['next'] ?? '(no-next)';
                $this->chat->reply("[DEBUG] choice_map {$idx} -> next='{$nxt}'");
            }
        }
    }


    public function getPlayerState()
    {
        return $this->playerState;
    }

    /**
     * Set player state. By default this does NOT overwrite active session state
     * after the engine has started. To force overwriting (e.g. during explicit load)
     * pass $force = true.
     *
     * This helps prevent accidental overwrites where external code calls
     * setPlayerState() on every incoming message.
     */
    public function setPlayerState($state, bool $force = false)
    {
        // If engine already started and not forced, ignore to avoid clobbering live state
        if ($this->started && !$force) {
            if ($this->debug) {
                $this->chat->reply("[DEBUG] setPlayerState() call ignored because engine already started. Use setPlayerState(\$state, true) to force.");
                // Show small backtrace to help find the caller
                $bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 6);
                $this->chat->reply("[DEBUG] calltrace (most recent caller first):");
                foreach ($bt as $i => $frame) {
                    // skip frames that are internal to this class
                    $file = $frame['file'] ?? '(internal)';
                    $line = $frame['line'] ?? '?';
                    $func = ($frame['class'] ?? '') . ($frame['type'] ?? '') . ($frame['function'] ?? '');
                    $this->chat->reply("[DEBUG]  #{$i} {$func} @ {$file}:{$line}");
                }
            }
            return;
        }

        // Normal merge behavior (allowed during init or when forced)
        $this->playerState = array_merge($this->playerState, $state);

        // If a current_node is present in state and matches a known node, set it up
        if (!empty($this->playerState['current_node']) && isset($this->nodes[$this->playerState['current_node']])) {
            $this->currentNode = $this->nodes[$this->playerState['current_node']];
            if ($this->debug) {
                $this->chat->reply("[DEBUG] setPlayerState applied -> current_node set to '" . $this->playerState['current_node'] . "'");
            }
        } else {
            // if no valid current_node, ensure playerState key is absent to avoid confusion
            if (!empty($this->playerState['current_node']) && !isset($this->nodes[$this->playerState['current_node']])) {
                unset($this->playerState['current_node']);
                if ($this->debug) {
                    $this->chat->reply("[DEBUG] setPlayerState: provided current_node not found in nodes; removed key.");
                }
            }
        }
    }
}
