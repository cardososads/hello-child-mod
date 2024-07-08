<?php

class AudioPlayer {
    private $audio_manager;

    public function __construct(AudioManager $audio_manager) {
        $this->audio_manager = $audio_manager;
    }

    public function renderAudios($audio_keys) {
        $audios = $this->audio_manager->getAudios($audio_keys);
        $output = '';

        foreach ($audios as $key => $audio) {
            if (!empty($audio['src'])) {
                $output .= $this->renderAudio($key, $audio['src'], $audio['subtitle']);
            }
        }

        return $output;
    }

    private function renderAudio($id, $src, $subtitle = '', $autoplay = false, $display_style = 'none') {
        ob_start();
        ?>
        <audio id="<?php echo $id; ?>" controls <?php if ($autoplay) echo 'autoplay'; ?> style="width: 100%; display: <?php echo $display_style; ?>">
            <source src="<?php echo $src; ?>" type="audio/mpeg">
        </audio>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                <?php echo $subtitle; ?>

                const audio = document.getElementById('<?php echo $id; ?>');
                const textDiv = document.getElementById('text');
                let timeoutIDs = [];

                const handleSubtitles = (subtitles) => {
                    timeoutIDs.forEach(id => clearTimeout(id));  // Limpa timeouts anteriores
                    timeoutIDs = [];

                    subtitles.forEach(subtitle => {
                        const timeoutID = setTimeout(() => {
                            textDiv.textContent = subtitle.text;
                        }, subtitle.time * 1000);
                        timeoutIDs.push(timeoutID);
                    });
                };

                audio.addEventListener('play', () => handleSubtitles(subtitles));
                audio.addEventListener('pause', () => timeoutIDs.forEach(id => clearTimeout(id)));
                audio.addEventListener('seeked', () => {
                    timeoutIDs.forEach(id => clearTimeout(id));
                    timeoutIDs = [];

                    const currentTime = audio.currentTime;
                    subtitles.forEach(subtitle => {
                        if (subtitle.time >= currentTime) {
                            const timeoutID = setTimeout(() => {
                                textDiv.textContent = subtitle.text;
                            }, (subtitle.time - currentTime) * 1000);
                            timeoutIDs.push(timeoutID);
                        }
                    });
                });
                audio.addEventListener('ended', () => {
                    textDiv.textContent = "";
                });
            });
        </script>
        <?php
        return ob_get_clean();
    }
}
