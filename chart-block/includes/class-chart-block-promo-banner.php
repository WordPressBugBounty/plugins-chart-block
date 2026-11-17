<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'chart_PromoBanner' ) ) {

    class chart_PromoBanner {

        const DISMISSED_KEY   = 'chart_promo_banner_dismissed_permanent';
        const EXPIRATION_DATE = '2026-12-05 23:59:59';

        /**
         * Chart Block pricing URL.
         */
        const COUPON_URL = 'https://bplugins.com/products/chart-block/pricing/?utm_source=admin&utm_medium=dashboard&utm_campaign=bfcm2026';

        public function __construct() {

            add_action( 'admin_notices', [ $this, 'render' ] );
            add_action( 'admin_head', [ $this, 'enqueue_styles' ] );
            add_action( 'admin_footer', [ $this, 'enqueue_scripts' ] );
        }

        /* ---------------------------------------------------------
         * Screen, Expiration, Dismissal & Pro Helpers
         * --------------------------------------------------------- */

        /**
         * Check whether the current screen belongs to Chart Block.
         */
        private function is_plugin_screen(): bool {

            $screen = get_current_screen();

            if ( $screen ) {

                $id        = $screen->id ?? '';
                $base      = $screen->base ?? '';
                $post_type = $screen->post_type ?? '';

                if (
                    strpos( $id, 'chart-block' ) !== false ||
                    strpos( $base, 'chart-block' ) !== false ||
                    $post_type === 'chart_block'
                ) {
                    return true;
                }
            }

            if ( isset( $_GET['page'] ) ) {

                $page = sanitize_key( wp_unslash( $_GET['page'] ) );

                if (
                    strpos( $page, 'chart-block' ) !== false ||
                    strpos( $page, 'chart_block' ) !== false
                ) {
                    return true;
                }
            }

            return false;
        }

        /**
         * Check whether the promotional campaign has expired.
         */
        private function is_expired(): bool {

            $expiry_timestamp = strtotime( self::EXPIRATION_DATE );

            $current_time = current_time( 'timestamp' );

            return $current_time > $expiry_timestamp;
        }

        /**
         * Determine whether the banner should be skipped.
         */
        private function should_skip(): bool {

            // Only show inside Chart Block admin screens.
            if ( ! $this->is_plugin_screen() ) {
                return true;
            }

            // Hide after December 5, 2026.
            if ( $this->is_expired() ) {
                return true;
            }

            // Hardcoded Free version check.
            $is_premium = false;

            if ( $is_premium ) {
                return true;
            }

            return false;
        }

        /* ---------------------------------------------------------
         * CSS Styles
         * --------------------------------------------------------- */

        public function enqueue_styles(): void {

            if ( $this->should_skip() ) {
                return;
            }
            ?>

            <style id="chart-promo-banner-css">

            #chart-promo-banner {
                --bpl-primary: #146ef5;
                --bpl-primary-hover: #0d5ecc;
                --bpl-secondary: #ff7a00;
                --bpl-dark: #070127;
                --bpl-text: #1b2e4b;
                --bpl-muted: #475569;

                background: #ffffff;
                border: 1px solid #cbd5e1;
                border-radius: 8px;
                box-shadow: 0 3px 12px rgba(7, 1, 39, 0.05);
                display: flex !important;
                align-items: stretch;
                min-height: 84px;
                overflow: hidden;
                position: relative;
                margin: 14px 0 16px 0;
                padding: 0;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
                box-sizing: border-box;
                animation: chartBannerSlide .3s ease both;
            }

            @keyframes chartBannerSlide {
                from {
                    opacity: 0;
                    transform: translateY(-6px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            /* Left Brand Ribbon */

            #chart-promo-banner .chart-brand-ribbon {
                width: 140px;
                min-width: 140px;
                background: #393CA3;
                color: #ffffff;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 12px 10px;
                gap: 6px;
                flex-shrink: 0;
            }

            #chart-promo-banner .chart-brand-ribbon img {
                width: 70px;
                height: 70px;
                object-fit: contain;
                filter: drop-shadow(0 2px 5px rgba(0, 0, 0, 0.35));
            }

            #chart-promo-banner .chart-brand-ribbon .chart-ribbon-title {
                font-size: 10.5px;
                font-weight: 800;
                letter-spacing: .7px;
                text-transform: uppercase;
                color: #ffffff;
                text-align: center;
                line-height: 1.2;
            }

            /* Main Content */

            #chart-promo-banner .chart-content-zone {
                flex: 1;
                padding: 14px 22px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 20px;
                flex-wrap: wrap;
                padding-right: 38px;
            }

            /* Text Stack */

            #chart-promo-banner .chart-text-stack {
                display: flex;
                flex-direction: column;
                gap: 6px;
            }

            #chart-promo-banner .chart-headline {
                font-size: 14px;
                font-weight: 700;
                color: var(--bpl-dark);
                display: flex;
                align-items: center;
                gap: 8px;
                margin: 0;
                line-height: 1.3;
            }

            /* Feature Checklist */

            #chart-promo-banner .chart-feature-chips {
                display: flex;
                align-items: center;
                gap: 12px;
                font-size: 12px;
                color: var(--bpl-muted);
                flex-wrap: wrap;
                margin-top:10px;
            }

            #chart-promo-banner .chart-feature-chips span {
                display: inline-flex;
                align-items: center;
                gap: 4px;
            }

            #chart-promo-banner .chart-feature-chips .check-icon {
                color: #10b981;
                font-weight: 800;
            }

            /* CTA Button */

            #chart-promo-banner .chart-btn-cta {
                background: var(--bpl-primary);
                color: #ffffff !important;
                padding: 9px 20px;
                border-radius: 5px;
                font-size: 12.5px;
                font-weight: 600;
                text-decoration: none !important;
                box-shadow: 0 2px 8px rgba(20, 110, 245, 0.32);
                transition: background-color .15s ease, transform .15s ease, box-shadow .15s ease;
                white-space: nowrap;
                display: inline-flex;
                align-items: center;
                gap: 6px;
            }

            #chart-promo-banner .chart-btn-cta:hover {
                background: var(--bpl-primary-hover);
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(20, 110, 245, 0.45);
            }

            /* Dismiss Button */

            #chart-promo-banner .chart-banner-dismiss-btn {
                position: absolute;
                top: 8px;
                right: 8px;
                background: none;
                border: none;
                cursor: pointer;
                color: #94a3b8;
                font-size: 20px;
                line-height: 1;
                padding: 6px;
                transition: color .15s ease;
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 10;
            }

            #chart-promo-banner .chart-banner-dismiss-btn:hover {
                color: var(--bpl-dark);
            }

            /* Responsive */

            @media (max-width: 960px) {
                #chart-promo-banner .chart-brand-ribbon {
                    width: 110px;
                    min-width: 110px;
                }
            }

            @media (max-width: 782px) {

                #chart-promo-banner {
                    flex-direction: column;
                    align-items: stretch;
                }

                #chart-promo-banner .chart-brand-ribbon {
                    width: 100%;
                    flex-direction: row;
                    justify-content: flex-start;
                    padding: 8px 16px;
                }

                #chart-promo-banner .chart-content-zone {
                    flex-direction: column;
                    align-items: flex-start;
                    padding: 14px 16px;
                }
            }

            </style>

            <?php
        }

        /* ---------------------------------------------------------
         * Permanent Dismiss Script
         * --------------------------------------------------------- */

        public function enqueue_scripts(): void {

            if ( $this->should_skip() ) {
                return;
            }
            ?>

            <script id="chart-promo-banner-js">

            (function () {

                var storageKey = <?php echo wp_json_encode( self::DISMISSED_KEY ); ?>;

                var banner = document.getElementById('chart-promo-banner');
                var btn = document.getElementById('chart-banner-close');

                if (!banner) return;

                // Hide banner if already dismissed.
                try {

                    if (window.localStorage.getItem(storageKey) === '1') {
                        banner.remove();
                        return;
                    }

                } catch (error) {
                    // Continue if localStorage is unavailable.
                }

                if (!btn) return;

                btn.addEventListener('click', function(e) {

                    e.preventDefault();

                    // Save permanent dismissal in this browser.
                    try {

                        window.localStorage.setItem(storageKey, '1');

                    } catch (error) {
                        // Still close the banner for current page.
                    }

                    banner.style.transition = 'opacity .25s ease, transform .25s ease';
                    banner.style.opacity = '0';
                    banner.style.transform = 'translateY(-6px)';

                    setTimeout(function() {
                        banner.remove();
                    }, 260);

                });

            })();

            </script>

            <?php
        }

        /* ---------------------------------------------------------
         * Render Banner
         * --------------------------------------------------------- */

        public function render(): void {

            if ( $this->should_skip() ) {
                return;
            }

            $coupon_url = esc_url( self::COUPON_URL );

            // Chart Block WordPress.org icon.
            $icon_url = 'https://ps.w.org/chart-block/assets/icon.svg';

            ?>

            <div id="chart-promo-banner">

                <!-- Left Brand Ribbon -->

                <div class="chart-brand-ribbon">

                    <img
                        src="<?php echo esc_url( $icon_url ); ?>"
                        alt="<?php esc_attr_e( 'Chart Block', 'chart-block' ); ?>"
                    />

                    <span class="chart-ribbon-title">
                        <?php esc_html_e( 'Chart Block', 'chart-block' ); ?>
                    </span>

                </div>

                <!-- Main Content Zone -->

                <div class="chart-content-zone">

                    <div class="chart-text-stack">

                        <div class="chart-headline">

                            <span>
                                <?php
                                esc_html_e(
                                    'Chart Block Biggest Black Friday Sale Is Live – Save Up to 45%!',
                                    'chart-block'
                                );
                                ?>
                            </span>

                        </div>

                        <div>
                            <span>
                                <?php
                                esc_html_e(
                                    'Create stunning, interactive charts with Bar, Line, Pie, and more! Visualize your data beautifully with customizable styles, advanced controls, and powerful data visualization features.',
                                    'chart-block'
                                );
                                ?>
                            </span>
                        </div>

                        <div class="chart-feature-chips">

                            <span>
                                <strong class="check-icon">✓</strong>
                                <?php esc_html_e( 'Advanced Chart Types', 'chart-block' ); ?>
                            </span>

                            <span>
                                <strong class="check-icon">✓</strong>
                                <?php esc_html_e( 'Customizable Chart Styles', 'chart-block' ); ?>
                            </span>

                            <span>
                                <strong class="check-icon">✓</strong>
                                <?php esc_html_e( 'Interactive Data Visualization', 'chart-block' ); ?>
                            </span>
                            <span>
                                <strong class="check-icon">✓</strong>
                                <?php esc_html_e( 'Live Data Connecting (Google Sheets & WooCommerce Orders)', 'chart-block' ); ?>
                            </span>

                            <span>
                                <strong class="check-icon">✓</strong>
                                <?php esc_html_e( 'Priority Support', 'chart-block' ); ?>
                            </span>

                        </div>

                    </div>

                    <!-- CTA Button -->

                    <a
                        href="<?php echo $coupon_url; ?>"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="chart-btn-cta"
                    >
                        <?php esc_html_e( 'Unlock PRO Features →', 'chart-block' ); ?>
                    </a>

                </div>

                <!-- Dismiss Button -->

                <button
                    id="chart-banner-close"
                    class="chart-banner-dismiss-btn"
                    title="<?php esc_attr_e( 'Dismiss', 'chart-block' ); ?>"
                    aria-label="<?php esc_attr_e( 'Dismiss banner', 'chart-block' ); ?>"
                    type="button"
                >
                    &times;
                </button>

            </div>

            <?php
        }
    }

    new chart_PromoBanner();
}
