<?php
/**
 * This code was generated by
 * ___ _ _ _ _ _    _ ____    ____ ____ _    ____ ____ _  _ ____ ____ ____ ___ __   __
 *  |  | | | | |    | |  | __ |  | |__| | __ | __ |___ |\ | |___ |__/ |__|  | |  | |__/
 *  |  |_|_| | |___ | |__|    |__| |  | |    |__] |___ | \| |___ |  \ |  |  | |__| |  \
 *
 * Twilio - Flex
 * This is the public Twilio REST API.
 *
 * NOTE: This class is auto generated by OpenAPI Generator.
 * https://openapi-generator.tech
 * Do not edit the class manually.
 */

namespace Twilio\Rest\FlexApi\V1;

use Twilio\Options;
use Twilio\Values;

abstract class InsightsSessionOptions
{
    /**
     * @param string $authorization The Authorization HTTP request header
     * @return CreateInsightsSessionOptions Options builder
     */
    public static function create(
        
        string $authorization = Values::NONE

    ): CreateInsightsSessionOptions
    {
        return new CreateInsightsSessionOptions(
            $authorization
        );
    }

}

class CreateInsightsSessionOptions extends Options
    {
    /**
     * @param string $authorization The Authorization HTTP request header
     */
    public function __construct(
        
        string $authorization = Values::NONE

    ) {
        $this->options['authorization'] = $authorization;
    }

    /**
     * The Authorization HTTP request header
     *
     * @param string $authorization The Authorization HTTP request header
     * @return $this Fluent Builder
     */
    public function setAuthorization(string $authorization): self
    {
        $this->options['authorization'] = $authorization;
        return $this;
    }

    /**
     * Provide a friendly representation
     *
     * @return string Machine friendly representation
     */
    public function __toString(): string
    {
        $options = \http_build_query(Values::of($this->options), '', ' ');
        return '[Twilio.FlexApi.V1.CreateInsightsSessionOptions ' . $options . ']';
    }
}

