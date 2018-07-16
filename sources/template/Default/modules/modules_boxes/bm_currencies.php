<?php
/**
 * bm_currencies.php
 * @copyright Copyright 2008 - http://www.innov-concept.com
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @license GPL 2 License & MIT Licence
 
*/

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  use ClicShopping\Apps\Marketing\BannerManager\Classes\Shop\Banner;

  class bm_currencies {
    public $code;
    public $group;
    public $title;
    public $description;
    public $sort_order;
    public $enabled = false;
    public $pages;

    public function  __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);
      $this->title = CLICSHOPPING::getDef('module_boxes_currencies_title');
      $this->description = CLICSHOPPING::getDef('module_boxes_currencies_description');

      if ( defined('MODULE_BOXES_CURRENCIES_STATUS') ) {
        $this->sort_order = MODULE_BOXES_CURRENCIES_SORT_ORDER;
        $this->enabled = (MODULE_BOXES_CURRENCIES_STATUS == 'True');
        $this->pages = MODULE_BOXES_CURRENCIES_DISPLAY_PAGES;
        $this->group = ((MODULE_BOXES_CURRENCIES_CONTENT_PLACEMENT == 'Left Column') ? 'boxes_column_left' : 'boxes_column_right');
      }
    }

    public function  execute() {
      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_Currencies = Registry::get('Currencies');
      $CLICSHOPPING_Service = Registry::get('Service');
      $CLICSHOPPING_Banner = Registry::get('Banner');

      if (substr(CLICSHOPPING::getBaseNameIndex(), 0, 8) != 'Checkout') {
        if (isset($CLICSHOPPING_Currencies) && is_object($CLICSHOPPING_Currencies) && (count($CLICSHOPPING_Currencies->currencies) > 1)) {
          reset($CLICSHOPPING_Currencies->currencies);
          $currencies_array = array();

          foreach($CLICSHOPPING_Currencies->currencies as $key => $value) {
            $currencies_array[] = ['id' => $key,
                                   'text' => $value['title']
                                  ];
          }

          $hidden_get_variables = '';

          foreach ( $_GET as $key => $value ) {
            if ( is_string($value) && ($key != 'currency') && ($key != session_name()) && ($key != 'x') && ($key != 'y') ) {
              $hidden_get_variables .= HTML::hiddenField($key, $value);
            }
          }

          if ($CLICSHOPPING_Service->isStarted('Banner') ) {
            if ($banner = $CLICSHOPPING_Banner->bannerExists('dynamic',  MODULE_BOXES_CURRENCIES_BANNER_GROUP)) {
              $currencies_banner = $CLICSHOPPING_Banner->displayBanner('static', $banner) . '<br /><br />';
            }
          }

          $currency ='<!-- Boxe currencies start -->' . "\n";

          $output = HTML::form('currencies', CLICSHOPPING::link('index.php', null, false), 'get', null, ['session_id' => true]);

          $output .= HTML::selectMenu('currency', $currencies_array, $_SESSION['currency'], 'class="boxePullDownsideCurrencies" onchange="this.form.submit();"') . $hidden_get_variables;
          $output .= '</form>';

          ob_start();
          require($CLICSHOPPING_Template->getTemplateModules('/modules_boxes/content/currencies'));
          $currency .= ob_get_clean();

          $currency .='<!-- Boxe currencies end -->' . "\n";

          $CLICSHOPPING_Template->addBlock($currency, $this->group);
        }
      }
    }

    public function  isEnabled() {
      return $this->enabled;
    }

    public function  check() {
      return defined('MODULE_BOXES_CURRENCIES_STATUS');
    }

    public function  install() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Souhaitez-vous activer ce module ?',
          'configuration_key' => 'MODULE_BOXES_CURRENCIES_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Souhaitez vous activer ce module à votre boutique ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Veuillez choisir l\'emplacement du contenu de la boxe',
          'configuration_key' => 'MODULE_BOXES_CURRENCIES_CONTENT_PLACEMENT',
          'configuration_value' => 'Right Column',
          'configuration_description' => 'Parmi les options qui vous sont proposées , veuillez en choisir une. <strong>Note :</strong><br /><br /><i>- Column right : Colonne de droite<br />- Column left : Colonne de gauche</i>',
          'configuration_group_id' => '6',
          'sort_order' => '2',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'Left Column\', \'Right Column\'),',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Veuillez indiquer le groupe d\'appartenance de la banniere',
          'configuration_key' => 'MODULE_BOXES_CURRENCIES_BANNER_GROUP',
          'configuration_value' => SITE_THEMA.'_boxe_currencies',
          'configuration_description' => 'Veuillez indiquer le groupe d\'appartenance de la bannière<br /><br /><strong>Note :</strong><br /><i>Le groupe sera à indiquer lors de la création de la bannière dans la section Marketing / Gestion des bannières</i>',
          'configuration_group_id' => '6',
          'sort_order' => '3',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Ordre de tri d\'affichage',
          'configuration_key' => 'MODULE_BOXES_CURRENCIES_SORT_ORDER',
          'configuration_value' => '120',
          'configuration_description' => 'Ordre de tri pour l\'affichage (Le plus petit nombre est montré en premier)',
          'configuration_group_id' => '6',
          'sort_order' => '4',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Veuillez indiquer ou la boxe doit s\'afficher',
          'configuration_key' => 'MODULE_BOXES_CURRENCIES_DISPLAY_PAGES',
          'configuration_value' => 'all',
          'configuration_description' => 'Sélectionnez les pages où la boxe doit être présente.',
          'configuration_group_id' => '6',
          'sort_order' => '5',
          'set_function' => 'clic_cfg_set_select_pages_list',
          'date_added' => 'now()'
        ]
      );


      return $CLICSHOPPING_Db->save('configuration', ['configuration_value' => '1'],
                                               ['configuration_key' => 'WEBSITE_MODULE_INSTALLED']
                              );

    }

    public function  remove() {
      return Registry::get('Db')->exec('delete from :table_configuration where configuration_key in ("' . implode('", "', $this->keys()) . '")');
    }

    public function  keys() {
      return array('MODULE_BOXES_CURRENCIES_STATUS',
                   'MODULE_BOXES_CURRENCIES_CONTENT_PLACEMENT',
                   'MODULE_BOXES_CURRENCIES_BANNER_GROUP',
                   'MODULE_BOXES_CURRENCIES_SORT_ORDER',
                   'MODULE_BOXES_CURRENCIES_DISPLAY_PAGES');
    }
  }
