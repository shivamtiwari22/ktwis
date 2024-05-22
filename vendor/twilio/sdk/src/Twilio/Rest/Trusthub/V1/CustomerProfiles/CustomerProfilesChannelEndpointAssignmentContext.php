<?php

/**
 * This code was generated by
 * ___ _ _ _ _ _    _ ____    ____ ____ _    ____ ____ _  _ ____ ____ ____ ___ __   __
 *  |  | | | | |    | |  | __ |  | |__| | __ | __ |___ |\ | |___ |__/ |__|  | |  | |__/
 *  |  |_|_| | |___ | |__|    |__| |  | |    |__] |___ | \| |___ |  \ |  |  | |__| |  \
 *
 * Twilio - Trusthub
 * This is the public Twilio REST API.
 *
 * NOTE: This class is auto generated by OpenAPI Generator.
 * https://openapi-generator.tech
 * Do not edit the class manually.
 */


namespace Twilio\Rest\Trusthub\V1\CustomerProfiles;

use Twilio\Exceptions\TwilioException;
use Twilio\Version;
use Twilio\InstanceContext;


class CustomerProfilesChannelEndpointAssignmentContext extends InstanceContext
    {
    /**
     * Initialize the CustomerProfilesChannelEndpointAssignmentContext
     *
     * @param Version $version Version that contains the resource
     * @param string $customerProfileSid The unique string that we created to identify the CustomerProfile resource.
     * @param string $sid The unique string that we created to identify the resource.
     */
    public function __construct(
        Version $version,
        $customerProfileSid,
        $sid
    ) {
        parent::__construct($version);

        // Path Solution
        $this->solution = [
        'customerProfileSid' =>
            $customerProfileSid,
        'sid' =>
            $sid,
        ];

        $this->uri = '/CustomerProfiles/' . \rawurlencode($customerProfileSid)
        .'/ChannelEndpointAssignments/' . \rawurlencode($sid)
        .'';
    }

    /**
     * Delete the CustomerProfilesChannelEndpointAssignmentInstance
     *
     * @return bool True if delete succeeds, false otherwise
     * @throws TwilioException When an HTTP error occurs.
     */
    public function delete(): bool
    {

        return $this->version->delete('DELETE', $this->uri);
    }


    /**
     * Fetch the CustomerProfilesChannelEndpointAssignmentInstance
     *
     * @return CustomerProfilesChannelEndpointAssignmentInstance Fetched CustomerProfilesChannelEndpointAssignmentInstance
     * @throws TwilioException When an HTTP error occurs.
     */
    public function fetch(): CustomerProfilesChannelEndpointAssignmentInstance
    {

        $payload = $this->version->fetch('GET', $this->uri);

        return new CustomerProfilesChannelEndpointAssignmentInstance(
            $this->version,
            $payload,
            $this->solution['customerProfileSid'],
            $this->solution['sid']
        );
    }


    /**
     * Provide a friendly representation
     *
     * @return string Machine friendly representation
     */
    public function __toString(): string
    {
        $context = [];
        foreach ($this->solution as $key => $value) {
            $context[] = "$key=$value";
        }
        return '[Twilio.Trusthub.V1.CustomerProfilesChannelEndpointAssignmentContext ' . \implode(' ', $context) . ']';
    }
}
