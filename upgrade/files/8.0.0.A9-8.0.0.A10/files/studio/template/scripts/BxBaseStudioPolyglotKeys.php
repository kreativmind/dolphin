<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) BoonEx Pty Limited - http://www.boonex.com/
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 *
 * @defgroup    TridentView Trident Studio Representation classes
 * @ingroup     TridentStudio
 * @{
 */

bx_import('BxDolStudioPolyglotKeys');

class BxBaseStudioPolyglotKeys extends BxDolStudioPolyglotKeys
{
    function __construct($aOptions, $oTemplate = false)
    {
        parent::__construct($aOptions, $oTemplate);

        $this->_aOptions['actions_single']['edit']['attr']['title'] = _t('_adm_pgt_btn_edit_title');
        $this->_aOptions['actions_single']['delete']['attr']['title'] = _t('_adm_pgt_btn_delete_title');
    }

    public function performActionAdd()
    {
        $sAction = 'add';

        $aLanguages = array();
        $iLanguages = $this->oDb->getLanguagesBy(array('type' => 'all_key_id'), $aLanguages);

        $aForm = array(
            'form_attrs' => array(
                'id' => 'adm-lang-new-key-form',
                'action' => BX_DOL_URL_ROOT . 'grid.php?o=' . $this->_sObject . '&a=' . $sAction,
                'method' => 'post'
            ),
            'params' => array(
                'db' => array(
                    'table' => 'sys_localization_keys',
                    'key' => 'ID',
                    'uri' => '',
                    'uri_title' => '',
                    'submit_name' => 'do_submit'
                ),
            ),
            'inputs' => array(
                'key' => array(
                    'type' => 'text',
                    'name' => 'key',
                    'caption' => _t('_adm_pgt_txt_nkp_key_name'),
                    'value' => '',
                    'db' => array (
                        'pass' => 'Xss',
                    ),
                ),
                'category_id' => array(
                    'type' => 'select',
                    'name' => 'category_id',
                    'caption' => _t('_adm_pgt_txt_nkp_module'),
                    'values' => $this->oDb->getCategories(),
                    'value' => '1',
                    'db' => array (
                        'pass' => 'Int',
                    ),
                ),

            )
        );

        foreach($aLanguages as $aLanguage) {
            $sName = 'language_' . $aLanguage['id'];
            $aForm['inputs'][$sName] = array(
                'type' => 'textarea',
                'name' => $sName,
                'caption' => $aLanguage['title'],
                'value' => '',
                'db' => array (
                    'pass' => 'XssHtml',
                ),
            );
        }

        $aForm['inputs'] = array_merge($aForm['inputs'], array(
            'languages' => array(
                'type' => 'hidden',
                'name' => 'languages',
                'value' => implode(',', array_keys($aLanguages)),
                'db' => array (
                    'pass' => 'Xss',
                ),
            ),
            'controls' => array(
                'name' => 'controls',
                'type' => 'input_set',
                array(
                    'type' => 'submit',
                    'name' => 'do_submit',
                    'value' => _t('_adm_pgt_btn_nkp_create'),
                ),
                array (
                    'type' => 'reset',
                    'name' => 'close',
                    'value' => _t('_adm_pgt_btn_nkp_close'),
                    'attrs' => array(
                        'onclick' => "$('.bx-popup-applied:visible').dolPopupHide()",
                        'class' => 'bx-def-margin-sec-left',
                    ),
                )
            )
        ));

        bx_import('BxTemplStudioFormView');
        $oForm = new BxTemplStudioFormView($aForm);
        $oForm->initChecker();

        if($oForm->isSubmittedAndValid()) {
            $mixedResult = $this->add($oForm);

            if(is_int($mixedResult))
                $aRes = array('grid' => $this->getCode(false), 'blink' => $mixedResult);
            else
                $aRes = array('msg' => $mixedResult);

            $this->_echoResultJson($aRes, true);
        } else {
            bx_import('BxTemplStudioFunctions');
            $sContent = BxTemplStudioFunctions::getInstance()->popupBox('adm-lang-new-key-popup', _t('_adm_pgt_txt_nkp_add_popup'), $this->_oTemplate->parseHtmlByName('pgt_new_key.html', array(
                'form_id' => $aForm['form_attrs']['id'],
                'form' => $oForm->getCode(true),
                'object' => $this->_sObject,
                'action' => $sAction
            )));

            $this->_echoResultJson(array('popup' => array('html' => $sContent, 'options' => array('closeOnOuterClick' => false))), true);
        }
    }

    public function performActionEdit()
    {
        $oTemplate = BxDolStudioTemplate::getInstance();

        $aIds = bx_get('ids');
        if(!$aIds || !is_array($aIds)) {
            $iId = (int)bx_get('id');
            if(!$iId) {
                $this->_echoResultJson(array());
                exit;
            }

            $aIds = array($iId);
        }

        $iId = $aIds[0];
        $sAction = 'edit';

        $aLanguages = array();
        $iLanguages = $this->oDb->getLanguagesBy(array('type' => 'all_key_id'), $aLanguages);

        $aKey = $this->oDb->getKeyFullInfo($iId);
        $aForm = array(
            'form_attrs' => array(
                'id' => 'adm-lang-edit-key-form',
                'action' => BX_DOL_URL_ROOT . 'grid.php?o=' . $this->_sObject . '&a=' . $sAction,
                'method' => 'post'
            ),
            'params' => array(
                'db' => array(
                    'table' => 'sys_localization_keys',
                    'key' => 'ID',
                    'uri' => '',
                    'uri_title' => '',
                    'submit_name' => 'do_submit'
                ),
            ),
            'inputs' => array(
                'id' => array(
                    'type' => 'hidden',
                    'name' => 'id',
                    'value' => $iId,
                    'db' => array (
                        'pass' => 'Int',
                    ),
                ),
            )
        );

        foreach($aLanguages as $aLanguage) {
            $sName = 'language_' . $aLanguage['id'];
            $aForm['inputs'][$sName] = array(
                'type' => 'textarea',
                'name' => $sName,
                'caption' => $aLanguage['title'],
                'value' => isset($aKey['strings'][$aLanguage['name']]) ? $aKey['strings'][$aLanguage['name']]['string'] : '',
                'db' => array (
                    'pass' => 'XssHtml',
                ),
            );
        }

        $aForm['inputs'] = array_merge($aForm['inputs'], array(
            'languages' => array(
                'type' => 'hidden',
                'name' => 'languages',
                'value' => implode(',', array_keys($aLanguages)),
                'db' => array (
                    'pass' => 'Xss',
                ),
            ),
            'controls' => array(
                'name' => 'controls',
                'type' => 'input_set',
                array(
                    'type' => 'submit',
                    'name' => 'do_submit',
                    'value' => _t('_adm_pgt_btn_nkp_save'),
                ),
                array (
                    'type' => 'reset',
                    'name' => 'close',
                    'value' => _t('_adm_pgt_btn_nkp_close'),
                    'attrs' => array(
                        'onclick' => "$('.bx-popup-applied:visible').dolPopupHide()",
                        'class' => 'bx-def-margin-sec-left',
                    ),
                )
            )
        ));

        bx_import('BxTemplStudioFormView');
        $oForm = new BxTemplStudioFormView($aForm);
        $oForm->initChecker();

        if($oForm->isSubmittedAndValid()) {
            $mixedResult = $this->edit($oForm);

            if(is_int($mixedResult))
                $aRes = array('grid' => $this->getCode(false), 'blink' => $mixedResult);
            else
                $aRes = array('msg' => $mixedResult);

            $this->_echoResultJson($aRes, true);
        } else {
            bx_import('BxTemplStudioFunctions');
            $sContent = BxTemplStudioFunctions::getInstance()->popupBox('adm-lang-edit-key-popup', _t('_adm_pgt_txt_nkp_edit_popup', $aKey['key']), $this->_oTemplate->parseHtmlByName('pgt_new_key.html', array(
                'form_id' => $aForm['form_attrs']['id'],
                'form' => $oForm->getCode(true),
                'object' => $this->_sObject,
                'action' => $sAction
            )));

            $this->_echoResultJson(array('popup' => array('html' => $sContent, 'options' => array('closeOnOuterClick' => false))), true);
        }
    }

    public function performActionDelete()
    {
        $iAffected = 0;
        $aIds = bx_get('ids');
        if(!$aIds || !is_array($aIds)) {
            $this->_echoResultJson(array());
            exit;
        }

        bx_import('BxDolStudioLanguagesUtils');

        $aIdsAffected = array ();
        foreach($aIds as $iId) {
            if(!BxDolStudioLanguagesUtils::getInstance()->deleteLanguageStringById($iId, 0, false))
                continue;

            $aIdsAffected[] = $iId;
            $iAffected++;
        }

        $this->_echoResultJson($iAffected ? array('grid' => $this->getCode(false), 'blink' => $aIdsAffected) : array('msg' => _t('_adm_pgt_err_save')));
    }

    function getJsObject()
    {
        return 'oBxDolStudioPolyglotKeys';
    }

    function getCode($isDisplayHeader = true)
    {
        return $this->_oTemplate->parseHtmlByName('pgt_keys.html', array(
            'content' => parent::getCode($isDisplayHeader),
            'js_object' => $this->getJsObject(),
            'grid_object' => $this->_sObject
        ));
    }

    protected function _addJsCss()
    {
        parent::_addJsCss();
        $this->_oTemplate->addJs(array('jquery.form.min.js', 'polyglot_keys.js'));

        bx_import('BxTemplStudioFormView');
        $oForm = new BxTemplStudioFormView(array());
        $oForm->addCssJs();
    }

    protected function _getFilterControls ()
    {
        parent::_getFilterControls();

        $sContent = "";

        bx_import('BxTemplStudioFormView');
        $oForm = new BxTemplStudioFormView(array());

        $aInputModules = array(
            'type' => 'select',
            'name' => 'module',
            'attrs' => array(
                'id' => 'bx-grid-module-' . $this->_sObject,
                'onChange' => 'javascript:' . $this->getJsObject() . '.onChangeFilter()'
            ),
            'values' => array(
                'id-' . BX_DOL_LANGUAGE_CATEGORY_SYSTEM => '',
                'id-' . BX_DOL_LANGUAGE_CATEGORY_CUSTOM => ''
            )
        );

        $aCategories = $aCounter = array();
        $this->oDb->getCategoriesBy(array('type' => 'all'), $aCategories, false);
        $this->oDb->getKeysBy(array('type' => 'counter_by_category'), $aCounter, false);
        foreach($aCategories as $aCategory)
            $aInputModules['values']['id-' . $aCategory['id']] = $aCategory['name'] . " (" . (isset($aCounter[$aCategory['id']]) ? $aCounter[$aCategory['id']] : "0") . ")";;

        $aInputModules['values'] = array_merge(array('id-0' => _t('_adm_pgt_txt_all_modules')), $aInputModules['values']);

        $sContent .= $oForm->genRow($aInputModules);

        $aInputSearch = array(
            'type' => 'text',
            'name' => 'keyword',
            'attrs' => array(
                'id' => 'bx-grid-search-' . $this->_sObject,
                'onKeyup' => 'javascript:$(this).off(); ' . $this->getJsObject() . '.onChangeFilter()'
            )
        );

        $sContent .= $oForm->genRow($aInputSearch);

        return $sContent;
    }
}

/** @} */
