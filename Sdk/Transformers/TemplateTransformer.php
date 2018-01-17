<?php

namespace Skybox\Checkout\Sdk\Transformers;

use Skybox\Checkout\Sdk\Entities\Template;
use stdClass;

/***
 * Class TemplateTransformer
 * @package Skybox\Checkout\Sdk\Transformers
 */
class TemplateTransformer extends KTransformer
{

    /**
     * @var null|stdClass
     */
    public $cart = null;

    /**
     * TemplateTransformer constructor.
     *
     * @param stdClass $cart
     */
    public function __construct(stdClass $cart)
    {
        $this->cart = $cart;
    }

    /**
     * @param stdClass $obj
     *
     * @return Template
     */
    public function transform(stdClass $obj)
    {
        $template = new Template; /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
        $template->setNavBar($this->parseNavBar($obj->BarHtmlTemplate, $this->cart));
        $template->setVersion($obj->CssVersion);

        return $template;
    }

    /**
     * @param $html
     * @param $cart
     *
     * @return mixed
     */
    public function parseNavBar($html, $cart)
    {
        $replace_these = ['{CartItemCount}', '{CartCurrencyISOCode}', '{CartCityName}', '{CartCountryISOCode}'];
        $with_these    = [
            $cart->Cart->Count,
            $cart->Cart->Currency,
            $cart->Country->Name,
            $cart->Country->Iso,
        ];

        $template = str_replace($replace_these, $with_these, $html);

        return $template;
    }
}
