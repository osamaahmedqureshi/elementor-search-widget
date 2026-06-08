<?php
if (!defined('ABSPATH')) exit;

class HJ_ESW_Search_Widget extends \Elementor\Widget_Base {
    public function get_name() { return 'hj-search'; }
    public function get_title() { return __('Search', 'elementor-search-widget'); }
    public function get_icon() { return 'eicon-search'; }
    public function get_categories() { return ['hj-widgets', 'general']; }
    public function get_keywords() { return ['search', 'elementor', 'fullscreen', 'overlay']; }
    public function get_style_depends() { return ['hj-esw-style']; }
    public function get_script_depends() { return ['hj-esw-script']; }

    protected function register_controls() {
        $this->start_controls_section('section_general', ['label' => __('General', 'elementor-search-widget')]);
        $this->add_control('layout', [
            'label' => __('Layout', 'elementor-search-widget'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'classic',
            'options' => [
                'classic' => __('Classic', 'elementor-search-widget'),
                'minimal' => __('Minimal', 'elementor-search-widget'),
                'creative' => __('Creative', 'elementor-search-widget'),
                'fullscreen' => __('Full Screen', 'elementor-search-widget'),
                'halfscreen' => __('Half Screen', 'elementor-search-widget'),
            ],
        ]);
        $this->add_control('source', [
            'label' => __('Source', 'elementor-search-widget'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'posts',
            'options' => ['posts'=>__('Posts','elementor-search-widget'), 'pages'=>__('Pages','elementor-search-widget'), 'products'=>__('Products','elementor-search-widget'), 'all'=>__('Posts + Pages','elementor-search-widget')],
        ]);
        $this->add_control('placeholder', ['label'=>__('Placeholder','elementor-search-widget'), 'type'=>\Elementor\Controls_Manager::TEXT, 'default'=>__('Search...','elementor-search-widget')]);
        $this->add_responsive_control('alignment', [
            'label'=>__('Alignment','elementor-search-widget'), 'type'=>\Elementor\Controls_Manager::CHOOSE,
            'options'=>['left'=>['title'=>'Left','icon'=>'eicon-text-align-left'], 'center'=>['title'=>'Center','icon'=>'eicon-text-align-center'], 'right'=>['title'=>'Right','icon'=>'eicon-text-align-right']],
            'default'=>'center',
            'selectors_dictionary'=>['left'=>'flex-start','center'=>'center','right'=>'flex-end'],
            'selectors'=>['{{WRAPPER}} .hj-esw-wrap'=>'justify-content: {{VALUE}};'],
        ]);
        $this->add_control('button_heading', ['label'=>__('Button','elementor-search-widget'), 'type'=>\Elementor\Controls_Manager::HEADING, 'separator'=>'before']);
        $this->add_control('button_type', ['label'=>__('Type','elementor-search-widget'), 'type'=>\Elementor\Controls_Manager::SELECT, 'default'=>'icon', 'options'=>['icon'=>'Icon','text'=>'Text','both'=>'Icon + Text']]);
        $this->add_control('button_text', ['label'=>__('Button Text','elementor-search-widget'), 'type'=>\Elementor\Controls_Manager::TEXT, 'default'=>__('Search','elementor-search-widget'), 'condition'=>['button_type!'=>'icon']]);
        $this->add_control('button_icon', ['label'=>__('Icon','elementor-search-widget'), 'type'=>\Elementor\Controls_Manager::ICONS, 'default'=>['value'=>'fas fa-search','library'=>'fa-solid'], 'condition'=>['button_type!'=>'text']]);
        $this->add_control('panel_side', ['label'=>__('Half Screen Side','elementor-search-widget'), 'type'=>\Elementor\Controls_Manager::SELECT, 'default'=>'right', 'options'=>['right'=>'Right','left'=>'Left'], 'condition'=>['layout'=>'halfscreen']]);
        $this->add_control('ajax_suggestions', ['label'=>__('AJAX Suggestions','elementor-search-widget'), 'type'=>\Elementor\Controls_Manager::SWITCHER, 'return_value'=>'yes', 'default'=>'yes']);
        $this->add_control('suggestions_limit', ['label'=>__('Suggestion Limit','elementor-search-widget'), 'type'=>\Elementor\Controls_Manager::NUMBER, 'default'=>5, 'min'=>1, 'max'=>12, 'condition'=>['ajax_suggestions'=>'yes']]);
        $this->end_controls_section();

        $this->start_controls_section('section_style_box', ['label'=>__('Box','elementor-search-widget'), 'tab'=>\Elementor\Controls_Manager::TAB_STYLE]);
        $this->add_responsive_control('width', ['label'=>__('Width','elementor-search-widget'), 'type'=>\Elementor\Controls_Manager::SLIDER, 'size_units'=>['px','%'], 'range'=>['px'=>['min'=>80,'max'=>1200], '%'=>['min'=>5,'max'=>100]], 'default'=>['unit'=>'%','size'=>100], 'selectors'=>['{{WRAPPER}} .hj-esw'=>'width: {{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('trigger_size', ['label'=>__('Trigger/Button Size','elementor-search-widget'), 'type'=>\Elementor\Controls_Manager::SLIDER, 'range'=>['px'=>['min'=>32,'max'=>120]], 'default'=>['size'=>50,'unit'=>'px'], 'selectors'=>['{{WRAPPER}} .hj-esw-trigger'=>'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .hj-esw-form'=>'min-height: {{SIZE}}{{UNIT}};']]);
        $this->add_control('box_bg', ['label'=>__('Background','elementor-search-widget'), 'type'=>\Elementor\Controls_Manager::COLOR, 'selectors'=>['{{WRAPPER}} .hj-esw-form'=>'background-color: {{VALUE}};']]);
        $this->add_group_control(\Elementor\Group_Control_Border::get_type(), ['name'=>'box_border', 'selector'=>'{{WRAPPER}} .hj-esw-form, {{WRAPPER}} .hj-esw-trigger']);
        $this->add_responsive_control('radius', ['label'=>__('Border Radius','elementor-search-widget'), 'type'=>\Elementor\Controls_Manager::DIMENSIONS, 'size_units'=>['px','%'], 'selectors'=>['{{WRAPPER}} .hj-esw-form, {{WRAPPER}} .hj-esw-trigger'=>'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->end_controls_section();

        $this->start_controls_section('section_style_input', ['label'=>__('Input','elementor-search-widget'), 'tab'=>\Elementor\Controls_Manager::TAB_STYLE]);
        $this->add_control('input_color', ['label'=>__('Text Color','elementor-search-widget'), 'type'=>\Elementor\Controls_Manager::COLOR, 'selectors'=>['{{WRAPPER}} .hj-esw-input'=>'color: {{VALUE}} !important; -webkit-text-fill-color: {{VALUE}} !important;']]);
        $this->add_control('placeholder_color', ['label'=>__('Placeholder Color','elementor-search-widget'), 'type'=>\Elementor\Controls_Manager::COLOR, 'selectors'=>['{{WRAPPER}} .hj-esw-input::placeholder'=>'color: {{VALUE}} !important; opacity:1;']]);
        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), ['name'=>'input_typography', 'selector'=>'{{WRAPPER}} .hj-esw-input']);
        $this->end_controls_section();

        $this->start_controls_section('section_style_button', ['label'=>__('Button','elementor-search-widget'), 'tab'=>\Elementor\Controls_Manager::TAB_STYLE]);
        $this->add_control('button_color', ['label'=>__('Color','elementor-search-widget'), 'type'=>\Elementor\Controls_Manager::COLOR, 'selectors'=>['{{WRAPPER}} .hj-esw-button, {{WRAPPER}} .hj-esw-trigger'=>'color: {{VALUE}};']]);
        $this->add_control('button_bg', ['label'=>__('Background','elementor-search-widget'), 'type'=>\Elementor\Controls_Manager::COLOR, 'selectors'=>['{{WRAPPER}} .hj-esw-button, {{WRAPPER}} .hj-esw-trigger'=>'background-color: {{VALUE}};']]);
        $this->add_control('button_hover_bg', ['label'=>__('Hover Background','elementor-search-widget'), 'type'=>\Elementor\Controls_Manager::COLOR, 'selectors'=>['{{WRAPPER}} .hj-esw-button:hover, {{WRAPPER}} .hj-esw-trigger:hover'=>'background-color: {{VALUE}};']]);
        $this->end_controls_section();

        $this->start_controls_section('section_style_overlay', ['label'=>__('Overlay / Panel','elementor-search-widget'), 'tab'=>\Elementor\Controls_Manager::TAB_STYLE]);
        $this->add_control('overlay_bg', ['label'=>__('Full Screen Background','elementor-search-widget'), 'type'=>\Elementor\Controls_Manager::COLOR, 'default'=>'rgba(10,10,20,.96)', 'selectors'=>['{{WRAPPER}} .hj-esw-overlay'=>'background-color: {{VALUE}};']]);
        $this->add_control('panel_bg', ['label'=>__('Half Screen Background','elementor-search-widget'), 'type'=>\Elementor\Controls_Manager::COLOR, 'default'=>'#111827', 'selectors'=>['{{WRAPPER}} .hj-esw-panel'=>'background-color: {{VALUE}};']]);
        $this->end_controls_section();
    }

    private function post_type_hidden($source) {
        if ($source === 'pages') echo '<input type="hidden" name="post_type" value="page">';
        if ($source === 'products' && post_type_exists('product')) echo '<input type="hidden" name="post_type" value="product">';
    }

    private function icon($settings) {
        if (($settings['button_type'] ?? 'icon') !== 'text') {
            \Elementor\Icons_Manager::render_icon($settings['button_icon'], ['aria-hidden'=>'true']);
        }
        if (($settings['button_type'] ?? 'icon') !== 'icon') {
            echo '<span class="hj-esw-btn-text">' . esc_html($settings['button_text'] ?? 'Search') . '</span>';
        }
    }

    private function form($settings, $extra = '') { ?>
        <form class="hj-esw-form <?php echo esc_attr($extra); ?>" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
            <input class="hj-esw-input" type="search" name="s" placeholder="<?php echo esc_attr($settings['placeholder'] ?? 'Search...'); ?>" autocomplete="off">
            <?php $this->post_type_hidden($settings['source'] ?? 'posts'); ?>
            <button class="hj-esw-button" type="submit" aria-label="<?php esc_attr_e('Search','elementor-search-widget'); ?>"><?php $this->icon($settings); ?></button>
            <div class="hj-esw-results" hidden></div>
        </form>
    <?php }

    private function css_dimension_value($value) {
        if (!is_array($value) || !isset($value['size']) || $value['size'] === '') return '';
        $unit = $value['unit'] ?? 'px';
        return $value['size'] . $unit;
    }

    private function portal_input_css($s, $portal_class) {
        $input_selector = 'body .' . $portal_class . ' .hj-esw-input';
        $placeholder_selector = 'body .' . $portal_class . ' .hj-esw-input::placeholder';
        $css = '';
        $rules = [];

        if (!empty($s['input_color'])) {
            $rules[] = 'color:' . $s['input_color'] . ' !important';
            $rules[] = '-webkit-text-fill-color:' . $s['input_color'] . ' !important';
        }

        if (!empty($s['input_typography_font_family'])) $rules[] = 'font-family:' . $s['input_typography_font_family'] . ' !important';
        if (!empty($s['input_typography_font_size'])) { $v = $this->css_dimension_value($s['input_typography_font_size']); if ($v) $rules[] = 'font-size:' . $v . ' !important'; }
        if (!empty($s['input_typography_font_weight'])) $rules[] = 'font-weight:' . $s['input_typography_font_weight'] . ' !important';
        if (!empty($s['input_typography_text_transform'])) $rules[] = 'text-transform:' . $s['input_typography_text_transform'] . ' !important';
        if (!empty($s['input_typography_font_style'])) $rules[] = 'font-style:' . $s['input_typography_font_style'] . ' !important';
        if (!empty($s['input_typography_text_decoration'])) $rules[] = 'text-decoration:' . $s['input_typography_text_decoration'] . ' !important';
        if (!empty($s['input_typography_line_height'])) { $v = $this->css_dimension_value($s['input_typography_line_height']); if ($v) $rules[] = 'line-height:' . $v . ' !important'; }
        if (!empty($s['input_typography_letter_spacing'])) { $v = $this->css_dimension_value($s['input_typography_letter_spacing']); if ($v) $rules[] = 'letter-spacing:' . $v . ' !important'; }
        if (!empty($s['input_typography_word_spacing'])) { $v = $this->css_dimension_value($s['input_typography_word_spacing']); if ($v) $rules[] = 'word-spacing:' . $v . ' !important'; }

        if ($rules) $css .= $input_selector . '{' . implode(';', $rules) . ';}';
        if (!empty($s['placeholder_color'])) $css .= $placeholder_selector . '{color:' . $s['placeholder_color'] . ' !important; opacity:1 !important;}';

        return $css;
    }

    protected function render() {
        $s = $this->get_settings_for_display();
        $layout = $s['layout'] ?? 'classic';
        $id = 'hj-esw-' . $this->get_id();
        $portal_class = 'hj-esw-portal-' . $this->get_id();
        $portal_css = $this->portal_input_css($s, $portal_class);
        ?>
        <?php if (!empty($portal_css)) : ?><style><?php echo esc_html($portal_css); ?></style><?php endif; ?>
        <div class="hj-esw-wrap">
            <div id="<?php echo esc_attr($id); ?>" class="hj-esw hj-esw-layout-<?php echo esc_attr($layout); ?> hj-esw-panel-<?php echo esc_attr($s['panel_side'] ?? 'right'); ?>" data-ajax="<?php echo esc_attr($s['ajax_suggestions'] ?? 'yes'); ?>" data-source="<?php echo esc_attr($s['source'] ?? 'posts'); ?>" data-limit="<?php echo esc_attr($s['suggestions_limit'] ?? 5); ?>">
                <?php if ($layout === 'classic') : ?>
                    <?php $this->form($s); ?>
                <?php elseif ($layout === 'minimal') : ?>
                    <button type="button" class="hj-esw-trigger" aria-expanded="false" aria-label="<?php esc_attr_e('Open Search','elementor-search-widget'); ?>"><?php $this->icon($s); ?></button>
                    <div class="hj-esw-dropdown"><?php $this->form($s); ?></div>
                <?php elseif ($layout === 'creative') : ?>
                    <?php $this->form($s, 'hj-esw-creative-form'); ?>
                <?php elseif ($layout === 'fullscreen') : ?>
                    <button type="button" class="hj-esw-trigger" aria-expanded="false" aria-label="<?php esc_attr_e('Open Search','elementor-search-widget'); ?>"><?php $this->icon($s); ?></button>
                    <div class="hj-esw-overlay <?php echo esc_attr($portal_class); ?>" aria-hidden="true">
                        <button type="button" class="hj-esw-close" aria-label="<?php esc_attr_e('Close Search','elementor-search-widget'); ?>">&times;</button>
                        <div class="hj-esw-modal-content"><?php $this->form($s, 'hj-esw-large-form'); ?></div>
                    </div>
                <?php elseif ($layout === 'halfscreen') : ?>
                    <button type="button" class="hj-esw-trigger" aria-expanded="false" aria-label="<?php esc_attr_e('Open Search','elementor-search-widget'); ?>"><?php $this->icon($s); ?></button>
                    <div class="hj-esw-panel-backdrop <?php echo esc_attr($portal_class); ?>" aria-hidden="true"></div>
                    <aside class="hj-esw-panel <?php echo esc_attr($portal_class); ?>" aria-hidden="true">
                        <button type="button" class="hj-esw-close" aria-label="<?php esc_attr_e('Close Search','elementor-search-widget'); ?>">&times;</button>
                        <div class="hj-esw-panel-content"><?php $this->form($s, 'hj-esw-large-form'); ?></div>
                    </aside>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }
}
