<?php
/**
 * Template for displaying upcoming hash runs
 *
 * This template can be overridden by copying it to your theme:
 * yourtheme/gh3-hash-runs/upcoming-runs.php
 *
 * @var WP_Query $runs The query object containing hash runs
 */

if (!defined('ABSPATH')) {
    exit;
}

$first = true;
?>
<div class="gh3-upcoming-runs">
    <?php while ($runs->have_posts()) : $runs->the_post();
        $run_number = get_post_meta(get_the_ID(), '_gh3_run_number', true);
        $run_date = get_post_meta(get_the_ID(), '_gh3_run_date', true);
        $start_time = get_post_meta(get_the_ID(), '_gh3_start_time', true);
        if (empty($start_time)) {
            $start_time = '19:30';
        }
        $hares = get_post_meta(get_the_ID(), '_gh3_hares', true);
        $location = get_post_meta(get_the_ID(), '_gh3_location', true);
        $what3words = get_post_meta(get_the_ID(), '_gh3_what3words', true);
        $maps_url = get_post_meta(get_the_ID(), '_gh3_maps_url', true);
        $oninn = get_post_meta(get_the_ID(), '_gh3_oninn', true);
        $notes = get_post_meta(get_the_ID(), '_gh3_notes', true);

        $formatted_date = $run_date ? date_i18n(get_option('date_format'), strtotime($run_date)) : '';
        $show_time = ($start_time !== '19:30');
        $formatted_time = $show_time ? date_i18n('H:i', strtotime($start_time)) : '';

        if ($first) :
            $first = false;
            ?>
            <!-- Featured Run (First Upcoming) -->
            <div class="gh3-featured-run">
                <div class="gh3-run-header">
                    <?php if ($run_number) : ?>
                        <span class="gh3-run-number"><?php printf(__('Run #%s', 'gh3-hash-runs'), esc_html($run_number)); ?></span>
                    <?php endif; ?>
                    <h3 class="gh3-run-title"><?php the_title(); ?></h3>
                </div>

                <?php if ($formatted_date) : ?>
                    <div class="gh3-run-date">
                        <strong><?php echo esc_html($formatted_date); ?></strong>
                        <?php if ($show_time) : ?>
                            <span class="gh3-note-time"><?php printf(__('Note Start Time - %s', 'gh3-hash-runs'), esc_html($formatted_time)); ?></span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if ($hares) : ?>
                    <div class="gh3-run-detail">
                        <span class="gh3-label"><?php _e('Hare(s):', 'gh3-hash-runs'); ?></span>
                        <span class="gh3-value"><?php echo esc_html($hares); ?></span>
                    </div>
                <?php endif; ?>

                <?php if ($location || $what3words || $maps_url) : ?>
                    <div class="gh3-run-detail gh3-run-location">
                        <span class="gh3-label"><?php _e('Location:', 'gh3-hash-runs'); ?></span>
                        <span class="gh3-value">
                            <?php if ($location) : ?>
                                <?php echo esc_html($location); ?>
                            <?php endif; ?>
                            <?php if ($what3words) : ?>
                                <span class="gh3-what3words">
                                    <a href="https://what3words.com/<?php echo esc_attr(ltrim($what3words, '/')); ?>" target="_blank" rel="noopener">
                                        <?php echo esc_html($what3words); ?>
                                    </a>
                                </span>
                            <?php endif; ?>
                            <?php if ($maps_url) : ?>
                                <a href="<?php echo esc_url($maps_url); ?>" target="_blank" rel="noopener" class="gh3-maps-link">
                                    <?php _e('View Map', 'gh3-hash-runs'); ?>
                                </a>
                            <?php endif; ?>
                        </span>
                    </div>
                <?php endif; ?>

                <?php if ($oninn) : ?>
                    <div class="gh3-run-detail">
                        <span class="gh3-label"><?php _e('On Inn:', 'gh3-hash-runs'); ?></span>
                        <span class="gh3-value"><?php echo esc_html($oninn); ?></span>
                    </div>
                <?php endif; ?>

                <?php if ($notes) : ?>
                    <div class="gh3-run-notes">
                        <?php echo wp_kses_post(wpautop($notes)); ?>
                    </div>
                <?php endif; ?>
            </div>

        <?php else : ?>
            <?php if (!isset($compact_started)) : $compact_started = true; ?>
                <!-- Upcoming Runs List -->
                <div class="gh3-upcoming-list">
                    <h4><?php _e('Coming Up', 'gh3-hash-runs'); ?></h4>
                    <ul>
            <?php endif; ?>

            <li class="gh3-compact-run">
                <div class="gh3-compact-header">
                    <span class="gh3-compact-date"><?php echo esc_html($formatted_date); ?><?php if ($show_time) : ?> <em class="gh3-note-time-compact">(<?php echo esc_html($formatted_time); ?>)</em><?php endif; ?></span>
                    <?php if ($run_number) : ?>
                        <span class="gh3-compact-number"><?php printf(__('Run #%s', 'gh3-hash-runs'), esc_html($run_number)); ?></span>
                    <?php endif; ?>
                    <span class="gh3-compact-title"><?php the_title(); ?></span>
                    <span class="gh3-expand-icon">▼</span>
                </div>
                <div class="gh3-compact-details" style="display: none;">
                    <?php if ($hares) : ?>
                        <div class="gh3-run-detail">
                            <span class="gh3-label"><?php _e('Hare(s):', 'gh3-hash-runs'); ?></span>
                            <span class="gh3-value"><?php echo esc_html($hares); ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if ($location || $what3words || $maps_url) : ?>
                        <div class="gh3-run-detail">
                            <span class="gh3-label"><?php _e('Location:', 'gh3-hash-runs'); ?></span>
                            <span class="gh3-value">
                                <?php echo esc_html($location); ?>
                                <?php if ($what3words) : ?>
                                    <a href="https://what3words.com/<?php echo esc_attr(ltrim($what3words, '/')); ?>" target="_blank" rel="noopener" class="gh3-what3words"><?php echo esc_html($what3words); ?></a>
                                <?php endif; ?>
                                <?php if ($maps_url) : ?>
                                    <a href="<?php echo esc_url($maps_url); ?>" target="_blank" rel="noopener" class="gh3-maps-link"><?php _e('View Map', 'gh3-hash-runs'); ?></a>
                                <?php endif; ?>
                            </span>
                        </div>
                    <?php endif; ?>
                    <?php if ($oninn) : ?>
                        <div class="gh3-run-detail">
                            <span class="gh3-label"><?php _e('On Inn:', 'gh3-hash-runs'); ?></span>
                            <span class="gh3-value"><?php echo esc_html($oninn); ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if ($notes) : ?>
                        <div class="gh3-run-notes"><?php echo wp_kses_post(wpautop($notes)); ?></div>
                    <?php endif; ?>
                </div>
            </li>
        <?php endif; ?>
    <?php endwhile; ?>

    <?php if (isset($compact_started)) : ?>
            </ul>
        </div>
    <?php endif; ?>
</div>
