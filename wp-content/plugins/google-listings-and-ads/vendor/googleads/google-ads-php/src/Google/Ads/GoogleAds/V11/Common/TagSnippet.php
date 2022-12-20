<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: google/ads/googleads/v11/common/tag_snippet.proto

namespace Google\Ads\GoogleAds\V11\Common;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * The site tag and event snippet pair for a TrackingCodeType.
 *
 * Generated from protobuf message <code>google.ads.googleads.v11.common.TagSnippet</code>
 */
class TagSnippet extends \Google\Protobuf\Internal\Message
{
    /**
     * The type of the generated tag snippets for tracking conversions.
     *
     * Generated from protobuf field <code>.google.ads.googleads.v11.enums.TrackingCodeTypeEnum.TrackingCodeType type = 1;</code>
     */
    protected $type = 0;
    /**
     * The format of the web page where the tracking tag and snippet will be
     * installed, e.g. HTML.
     *
     * Generated from protobuf field <code>.google.ads.googleads.v11.enums.TrackingCodePageFormatEnum.TrackingCodePageFormat page_format = 2;</code>
     */
    protected $page_format = 0;
    /**
     * The site tag that adds visitors to your basic remarketing lists and sets
     * new cookies on your domain.
     *
     * Generated from protobuf field <code>optional string global_site_tag = 5;</code>
     */
    protected $global_site_tag = null;
    /**
     * The event snippet that works with the site tag to track actions that
     * should be counted as conversions.
     *
     * Generated from protobuf field <code>optional string event_snippet = 6;</code>
     */
    protected $event_snippet = null;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type int $type
     *           The type of the generated tag snippets for tracking conversions.
     *     @type int $page_format
     *           The format of the web page where the tracking tag and snippet will be
     *           installed, e.g. HTML.
     *     @type string $global_site_tag
     *           The site tag that adds visitors to your basic remarketing lists and sets
     *           new cookies on your domain.
     *     @type string $event_snippet
     *           The event snippet that works with the site tag to track actions that
     *           should be counted as conversions.
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Google\Ads\GoogleAds\V11\Common\TagSnippet::initOnce();
        parent::__construct($data);
    }

    /**
     * The type of the generated tag snippets for tracking conversions.
     *
     * Generated from protobuf field <code>.google.ads.googleads.v11.enums.TrackingCodeTypeEnum.TrackingCodeType type = 1;</code>
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * The type of the generated tag snippets for tracking conversions.
     *
     * Generated from protobuf field <code>.google.ads.googleads.v11.enums.TrackingCodeTypeEnum.TrackingCodeType type = 1;</code>
     * @param int $var
     * @return $this
     */
    public function setType($var)
    {
        GPBUtil::checkEnum($var, \Google\Ads\GoogleAds\V11\Enums\TrackingCodeTypeEnum\TrackingCodeType::class);
        $this->type = $var;

        return $this;
    }

    /**
     * The format of the web page where the tracking tag and snippet will be
     * installed, e.g. HTML.
     *
     * Generated from protobuf field <code>.google.ads.googleads.v11.enums.TrackingCodePageFormatEnum.TrackingCodePageFormat page_format = 2;</code>
     * @return int
     */
    public function getPageFormat()
    {
        return $this->page_format;
    }

    /**
     * The format of the web page where the tracking tag and snippet will be
     * installed, e.g. HTML.
     *
     * Generated from protobuf field <code>.google.ads.googleads.v11.enums.TrackingCodePageFormatEnum.TrackingCodePageFormat page_format = 2;</code>
     * @param int $var
     * @return $this
     */
    public function setPageFormat($var)
    {
        GPBUtil::checkEnum($var, \Google\Ads\GoogleAds\V11\Enums\TrackingCodePageFormatEnum\TrackingCodePageFormat::class);
        $this->page_format = $var;

        return $this;
    }

    /**
     * The site tag that adds visitors to your basic remarketing lists and sets
     * new cookies on your domain.
     *
     * Generated from protobuf field <code>optional string global_site_tag = 5;</code>
     * @return string
     */
    public function getGlobalSiteTag()
    {
        return isset($this->global_site_tag) ? $this->global_site_tag : '';
    }

    public function hasGlobalSiteTag()
    {
        return isset($this->global_site_tag);
    }

    public function clearGlobalSiteTag()
    {
        unset($this->global_site_tag);
    }

    /**
     * The site tag that adds visitors to your basic remarketing lists and sets
     * new cookies on your domain.
     *
     * Generated from protobuf field <code>optional string global_site_tag = 5;</code>
     * @param string $var
     * @return $this
     */
    public function setGlobalSiteTag($var)
    {
        GPBUtil::checkString($var, True);
        $this->global_site_tag = $var;

        return $this;
    }

    /**
     * The event snippet that works with the site tag to track actions that
     * should be counted as conversions.
     *
     * Generated from protobuf field <code>optional string event_snippet = 6;</code>
     * @return string
     */
    public function getEventSnippet()
    {
        return isset($this->event_snippet) ? $this->event_snippet : '';
    }

    public function hasEventSnippet()
    {
        return isset($this->event_snippet);
    }

    public function clearEventSnippet()
    {
        unset($this->event_snippet);
    }

    /**
     * The event snippet that works with the site tag to track actions that
     * should be counted as conversions.
     *
     * Generated from protobuf field <code>optional string event_snippet = 6;</code>
     * @param string $var
     * @return $this
     */
    public function setEventSnippet($var)
    {
        GPBUtil::checkString($var, True);
        $this->event_snippet = $var;

        return $this;
    }

}
