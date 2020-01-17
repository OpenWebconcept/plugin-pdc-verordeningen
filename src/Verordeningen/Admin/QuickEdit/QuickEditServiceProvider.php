<?php
/**
 * Provider which adds QuickEdit possibilities.
 */

namespace OWC\PDC\Verordeningen\Admin\QuickEdit;

use OWC\PDC\Base\Foundation\ServiceProvider;
use \WP_Post;

/**
 * Provider which adds QuickEdit possibilities.
 */
class QuickEditServiceProvider extends ServiceProvider
{

    /**
     * Prefix for the quickedit.
     *
     * @var string $prefix
     */
    protected $prefix = '_pdc-verordening';

    /**
     * Name of posttype for the quickedit.
     *
     * @var string $postType
     */
    protected $postType = 'pdc-verordeningen';

    /**
     * Array holding all the quickedit handlers.
     *
     * @var array $quickEditHandlers
     */
    protected $quickEditHandlers = [];

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->setQuickEditHandlers();

        $this->plugin->loader->addAction('quick_edit_custom_box', $this, 'registerQuickEditHandler', 10, 2);
        $this->plugin->loader->addAction('save_post', $this, 'registerSavePostHandler', 10, 2);
        $this->plugin->loader->addAction('admin_footer', $this, 'renderFooterScript', 10, 1);
        $this->plugin->loader->addFilter('post_row_actions', $this, 'addRowActions', 10, 2);
    }

    /**
     * Adds the handlers.
     *
     * @param array $actions
     * @param  WP_Post     $post
     *
     * @return array
     */
    public function addRowActions(array $actions, WP_Post $post): array
    {
        foreach ($this->getQuickEditHandlers() as $key => $handler) {
            $foundValue = get_post_meta($post->ID, $handler['metaboxKey'], true);

            if (! $foundValue) {
                continue;
            }

            if (isset($actions['inline hide-if-no-js'])) {
                $newAttribute                    = sprintf('data-%s="%s"', esc_attr($key), esc_attr($foundValue));
                $actions['inline hide-if-no-js'] = str_replace('class=', "$newAttribute class=", $actions['inline hide-if-no-js']);
            }
        }

        return $actions;
    }

    /**
     * Render javascript inline for admin only.
     *
     * @return mixed
     */
    public function renderFooterScript()
    {
        $current_screen = get_current_screen();
        if ($current_screen->id !== 'edit-' . $this->postType || $current_screen->post_type !== $this->postType) {
            return;
        }

        // Ensure jQuery library loads
        wp_enqueue_script('jquery');
        wp_enqueue_style('jquery-ui-datepicker', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/themes/smoothness/jquery-ui.css');
        wp_enqueue_script('jquery-ui-datepicker'); ?>
<script type="text/javascript">
    jQuery(function($) {
        $(function() {
            $('input[name="post_password"]').each(function(i) {
                $(this).parent().parent().parent().remove();
            });
            $('input[name="post_name"]').each(function(i) {
                $(this).parent().parent().remove();
            });
            $('#_pdc-verordening-active-date').datepicker({
                altField: "#_pdc-verordening-active-date",
                dateFormat: 'dd-mm-yy',
                changeMonth: true,
                changeYear: true,
                showButtonPanel: true,
                minDate: 0
            });
        });

        $('#the-list').on('click', '.editinline', function(e) {
            e.preventDefault();
            inlineEditPost.revert();
            <?php foreach ($this->quickEditHandlers as $key => $handler) { ?>
            var value = $(this).data('<?php echo $key; ?>');
            $('#<?php echo $handler['metaboxKey']; ?>').val(value ? value : '');
            <?php } ?>
        });
    });

</script>
        <?php
    }

    /**
     * Adds the quickedit handlers.
     *
     * @param $columnName
     * @param $postType
     *
     * @return mixed
     */
    public function registerQuickEditHandler($columnName, $postType)
    {
        if (! in_array($columnName, array_keys($this->quickEditHandlers))) {
            return;
        }

        echo '<fieldset class="inline-edit-col-left clear">
				<div class="inline-edit-col">';

        foreach ($this->quickEditHandlers as $key => $handler) {
            if ($columnName !== $key) {
                continue;
            }

            $method = explode('-', $key);
            $method = array_map('ucfirst', $method);
            $method = implode('', $method);

            if (method_exists($this, $method)) {
                $this->$method($handler);
            }
        }

        echo '</div>
			</fieldset>';
    }

    /**
     * Adds active date to quickedit handler.
     *
     * @param $item
     *
     * @return mixed
     */
    protected function ActiveDate($item)
    {
        $value = get_post_meta(get_the_ID(), $item['metaboxKey'], true); ?>
<label class="aligncenter" for="<?php echo $item['metaboxKey']; ?>">
    <span class="title"><?php echo __($item['label'], 'pdc-verordeningen'); ?></span>
    <span class="input-text-wrap"><input type="text" id="<?php echo $item['metaboxKey']; ?>" name="<?php echo $item['metaboxKey']; ?>" value="<?php echo $value; ?>"></span>
</label>
        <?php
    }

    /**
     * Adds link to quickedit handlers.
     *
     * @param $item
     *
     * @return mixed
     */
    protected function Link($item)
    {
        $value = get_post_meta(get_the_ID(), $item['metaboxKey'], true); ?>
<label class="aligncenter" for="<?php echo $item['metaboxKey']; ?>">
    <span class="title"><?php echo __($item['label'], 'pdc-verordeningen'); ?></span>
    <span class="input-text-wrap"><input type="text" id="<?php echo $item['metaboxKey']; ?>" name="<?php echo $item['metaboxKey']; ?>" value="<?php echo $value; ?>"></span>
</label>
        <?php
    }

    /**
     * Adds newlink to quickedit handlers.
     *
     * @param $item
     *
     * @return mixed
     */
    protected function NewLink($item)
    {
        $value = get_post_meta(get_the_ID(), $item['metaboxKey'], true); ?>
<label class="aligncenter" for="<?php echo $item['metaboxKey']; ?>">
    <span class="title"><?php echo __($item['label'], 'pdc-verordeningen'); ?></span>
    <span class="input-text-wrap"><input type="text" id="<?php echo $item['metaboxKey']; ?>" name="<?php echo $item['metaboxKey']; ?>" value="<?php echo $value; ?>"></span>
</label>
        <?php
    }

    /**
     * Returns the handlers for the quick edit.
     *
     * @return array
     */
    public function setQuickEditHandlers()
    {
        return $this->quickEditHandlers = [
            'new-link'   => [
                'metaboxKey' => sprintf('%s-%s', $this->prefix, 'new-link'),
                'label'      => __('New link', 'pdc-verordeningen')
            ],
            'link'       => [
                'metaboxKey' => sprintf('%s-%s', $this->prefix, 'link'),
                'label'      => __('Link', 'pdc-verordeningen')
            ],
            'active-date' => [
                'metaboxKey' => sprintf('%s-%s', $this->prefix, 'active-date'),
                'label'      => __('Date new verordening active', 'pdc-verordeningen')
            ]
        ];
    }

    /**
     * Handles the save post handler.
     *
     * @param $post_id
     * @param $post
     *
     * @return void
     */
    public function registerSavePostHandler($post_id, $post)
    {
        if (wp_is_post_revision($post_id)) {
            return;
        }

        if (wp_is_post_autosave($post_id)) {
            return;
        }

        // if this correct post type?
        if ($post->post_type !== $this->postType) {
            return;
        }

        // does this user have permissions?
        if (! current_user_can('edit_post', $post_id)) {
            return;
        }

        foreach ($this->getQuickEditHandlers() as $key => $handler) {
            // update!
            if (isset($_POST["{$this->prefix}-{$key}"])) {
                update_post_meta($post_id, "{$this->prefix}-{$key}", $_POST["{$this->prefix}-{$key}"]);
            }
        }
    }

    /**
     * Gets the registered quickedit handlers.
     *
     * @return array
     */
    private function getQuickEditHandlers()
    {
        return $this->quickEditHandlers;
    }
}
