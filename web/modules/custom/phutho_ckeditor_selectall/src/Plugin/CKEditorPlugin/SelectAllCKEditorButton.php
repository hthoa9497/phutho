<?php

/**
 * @file
 * Contains \Drupal\phutho_ckeditor_selectall\Plugin\CKEditorPlugin\FormElementsCKEditorButton.
 */

namespace Drupal\phutho_ckeditor_selectall\Plugin\CKEditorPlugin;

use Drupal\ckeditor\CKEditorPluginBase;
use Drupal\editor\Entity\Editor;

/**
 * Defines the "selectall" plugin.
 *
 * NOTE: The plugin ID ('id' key) corresponds to the CKEditor plugin name.
 * It is the first argument of the CKEDITOR.plugins.add() function in the
 * plugin.js file.
 *
 * @CKEditorPlugin(
 *   id = "selectall",
 *   label = @Translation("SelectAll CKEditor Buttons")
 * )
 */
class SelectAllCKEditorButton extends CKEditorPluginBase {

  public function getButtons() {
    // Make sure that the path to the image matches the file structure of
    // the CKEditor plugin you are implementing.
    $path =  drupal_get_path('module', 'phutho_ckeditor_selectall') . '/js/plugins/selectall';

    return array(
      'SelectAll' => array(
        'label' => $this->t('Select all'),
        'image' => $path . '/icons/selectall.png',
      )
    );
  }

  /**
   * Implements CKEditorPluginInterface::getFile().
   *
   * Returns the additions to CKEDITOR.config for a specific CKEditor instance.
   *
   * The editor's settings can be retrieved via $editor->getSettings(), but be
   * aware that it may not yet contain plugin-specific settings, because the
   * user may not yet have configured the form.
   * If there are plugin-specific settings (verify with isset()), they can be
   * found at
   * @code
   * $settings = $editor->getSettings();
   * $plugin_specific_settings = $settings['plugins'][$plugin_id];
   * @endcode
   *
   * @param \Drupal\editor\Entity\Editor $editor
   *   A configured text editor object.
   * @return array
   *   A keyed array, whose keys will end up as keys under CKEDITOR.config.
   */
  public function getFile() {
    return drupal_get_path('module', 'phutho_ckeditor_selectall') . '/js/plugins/selectall/plugin.js';
  }

  /**
   * Implements CKEditorPluginInterface::isInternal().
   *
   * Indicates if this plugin is part of the optimized CKEditor build.
   *
   * Plugins marked as internal are implicitly loaded as part of CKEditor.
   *
   * @return bool
   */
  function isInternal() {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  function getDependencies(Editor $editor) {
    return array();
  }

  /**
   * {@inheritdoc}
   */
  function getLibraries(Editor $editor) {
    return array();
  }

  /**
   * {@inheritdoc}
   */
  public function getConfig(Editor $editor) {
    return array();
  }
}
