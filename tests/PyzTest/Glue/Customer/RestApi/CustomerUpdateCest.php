<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace PyzTest\Glue\Customer\RestApi;

use Codeception\Example;
use Codeception\Util\HttpCode;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\RestCustomersAttributesTransfer;
use Generated\Shared\Transfer\RestErrorMessageTransfer;
use PyzTest\Glue\Customer\CustomerApiTester;
use Spryker\Glue\CustomersRestApi\CustomersRestApiConfig;
use Spryker\Glue\RestRequestValidator\RestRequestValidatorConfig;
use Symfony\Component\HttpFoundation\Response;

/**
 * Auto-generated group annotations
 *
 * @group PyzTest
 * @group Glue
 * @group Customer
 * @group RestApi
 * @group CustomerUpdateCest
 * Add your own group annotations below this line
 * @group EndToEnd
 */
class CustomerUpdateCest
{
    /**
     * @var \PyzTest\Glue\Customer\RestApi\CustomerRestApiFixtures
     */
    protected $fixtures;

    /**
     * @var \Generated\Shared\Transfer\CustomerTransfer
     */
    protected $customerTransfer;

    /**
     * @param \PyzTest\Glue\Customer\CustomerApiTester $I
     *
     * @return void
     */
    public function _before(CustomerApiTester $I): void
    {
        /** @var \PyzTest\Glue\Customer\RestApi\CustomerRestApiFixtures $fixtures */
        $fixtures = $I->loadFixtures(CustomerRestApiFixtures::class);

        $this->fixtures = $fixtures;

        $this->customerTransfer = $I->haveCustomer(
            [
                CustomerTransfer::NEW_PASSWORD => 'change123',
                CustomerTransfer::PASSWORD => 'change123',
            ],
        );
        $I->confirmCustomer($this->customerTransfer);

        $oauthResponseTransfer = $I->haveAuthorizationToGlue($this->customerTransfer);
        $I->amBearerAuthenticated($oauthResponseTransfer->getAccessToken());
    }

    /**
     * @param \PyzTest\Glue\Customer\CustomerApiTester $I
     *
     * @return void
     */
    public function requestPatchCustomerUpdatesCustomerProfile(CustomerApiTester $I): void
    {
        $firstName = uniqid('name');
        $restCustomersAttributesTransfer = (new RestCustomersAttributesTransfer())
            ->setFirstName($firstName);

        $I->sendPatch(
            $I->formatUrl(
                '{resourceCustomers}/{customerReference}',
                [
                    'resourceCustomers' => CustomersRestApiConfig::RESOURCE_CUSTOMERS,
                    'customerReference' => $this->customerTransfer->getCustomerReference(),
                ],
            ),
            [
                'data' => [
                    'type' => CustomersRestApiConfig::RESOURCE_CUSTOMERS,
                    'id' => $this->customerTransfer->getCustomerReference(),
                    'attributes' => $restCustomersAttributesTransfer->modifiedToArray(true, true),
                ],
            ],
        );

        // Assert
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesOpenApiSchema();

        $I->seeSingleResourceHasSelfLink(
            $I->formatFullUrl(
                '{resourceCustomers}/{customerReference}',
                [
                    'resourceCustomers' => CustomersRestApiConfig::RESOURCE_CUSTOMERS,
                    'customerReference' => $this->customerTransfer->getCustomerReference(),
                ],
            ),
        );

        $I->amSure(sprintf('Returned resource is of type %s', CustomersRestApiConfig::RESOURCE_CUSTOMERS))
            ->whenI()
            ->seeResponseDataContainsSingleResourceOfType(CustomersRestApiConfig::RESOURCE_CUSTOMERS);

        $actualResponseAttributes = $I->grabDataFromResponseByJsonPath('$.data.attributes');
        $I->amSure(sprintf('Attribute %s has been updated', 'firstName'))
            ->whenI()
            ->assertSame($firstName, $actualResponseAttributes['firstName']);

        $I->amSure('Returned resource correct attributes')
            ->whenI()
            ->assertCustomersAttributes(
                $this->customerTransfer->setFirstName($firstName),
                $I->grabDataFromResponseByJsonPath('$.data.attributes'),
            );
    }

    /**
     * @param \PyzTest\Glue\Customer\CustomerApiTester $I
     *
     * @return void
     */
    public function requestPatchCustomerFailsToUseAnotherCustomersEmail(CustomerApiTester $I): void
    {
        $firstCustomerTransfer = $I->haveCustomer(
            [
                CustomerTransfer::NEW_PASSWORD => 'change123',
                CustomerTransfer::PASSWORD => 'change123',
            ],
        );

        $restCustomersAttributesTransfer = (new RestCustomersAttributesTransfer())
            ->setEmail($firstCustomerTransfer->getEmail());

        $I->sendPatch(
            $I->formatUrl(
                '{resourceCustomers}/{customerReference}',
                [
                    'resourceCustomers' => CustomersRestApiConfig::RESOURCE_CUSTOMERS,
                    'customerReference' => $this->customerTransfer->getCustomerReference(),
                ],
            ),
            [
                'data' => [
                    'type' => CustomersRestApiConfig::RESOURCE_CUSTOMERS,
                    'id' => $this->customerTransfer->getCustomerReference(),
                    'attributes' => $restCustomersAttributesTransfer->modifiedToArray(true, true),
                ],
            ],
        );

        // Assert
        $I->seeResponseCodeIs(Response::HTTP_UNPROCESSABLE_ENTITY);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesOpenApiSchema();

        $I->seeResponseErrorsHaveCode(CustomersRestApiConfig::RESPONSE_CODE_CUSTOMER_ALREADY_EXISTS);
        $I->seeResponseErrorsHaveStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $I->seeResponseErrorsHaveDetail(CustomersRestApiConfig::RESPONSE_MESSAGE_CUSTOMER_ALREADY_EXISTS);
    }

    /**
     * @param \PyzTest\Glue\Customer\CustomerApiTester $I
     *
     * @return void
     */
    public function requestPatchCustomerFailsToUseAnotherCustomersCustomerReference(CustomerApiTester $I): void
    {
        $firstCustomerTransfer = $I->haveCustomer(
            [
                CustomerTransfer::NEW_PASSWORD => 'change123',
                CustomerTransfer::PASSWORD => 'change123',
            ],
        );

        $restCustomersAttributesTransfer = (new RestCustomersAttributesTransfer())
            ->setFirstName(uniqid());

        $I->sendPatch(
            $I->formatUrl(
                '{resourceCustomers}/{customerReference}',
                [
                    'resourceCustomers' => CustomersRestApiConfig::RESOURCE_CUSTOMERS,
                    'customerReference' => $firstCustomerTransfer->getCustomerReference(),
                ],
            ),
            [
                'data' => [
                    'type' => CustomersRestApiConfig::RESOURCE_CUSTOMERS,
                    'id' => $firstCustomerTransfer->getCustomerReference(),
                    'attributes' => $restCustomersAttributesTransfer->modifiedToArray(true, true),
                ],
            ],
        );

        // Assert
        $I->seeResponseCodeIs(Response::HTTP_FORBIDDEN);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesOpenApiSchema();

        $I->seeResponseErrorsHaveCode(CustomersRestApiConfig::RESPONSE_CODE_CUSTOMER_UNAUTHORIZED);
        $I->seeResponseErrorsHaveStatus(Response::HTTP_FORBIDDEN);
        $I->seeResponseErrorsHaveDetail(CustomersRestApiConfig::RESPONSE_DETAILS_CUSTOMER_UNAUTHORIZED);
    }

    /**
     * @param \PyzTest\Glue\Customer\CustomerApiTester $I
     *
     * @return void
     */
    public function requestPatchCustomerFailsWithoutCustomerReference(CustomerApiTester $I): void
    {
        $restCustomersAttributesTransfer = (new RestCustomersAttributesTransfer())
            ->setFirstName(uniqid());

        $I->sendPatch(
            $I->formatUrl(
                '{resourceCustomers}/',
                [
                    'resourceCustomers' => CustomersRestApiConfig::RESOURCE_CUSTOMERS,
                ],
            ),
            [
                'data' => [
                    'type' => CustomersRestApiConfig::RESOURCE_CUSTOMERS,
                    'id' => $this->customerTransfer->getCustomerReference(),
                    'attributes' => $restCustomersAttributesTransfer->modifiedToArray(true, true),
                ],
            ],
        );

        // Assert
        $I->seeResponseCodeIs(Response::HTTP_BAD_REQUEST);
        $I->seeResponseIsJson();

        $I->seeResponseErrorsHaveStatus(Response::HTTP_BAD_REQUEST);
        /**
         * @uses \Spryker\Glue\GlueApplication\Rest\Request\RestRequestValidator::EXCEPTION_MESSAGE_RESOURCE_ID_IS_NOT_SPECIFIED
         */
        $I->seeResponseErrorsHaveDetail('Resource id is not specified.');
    }

    /**
     * @param \PyzTest\Glue\Customer\CustomerApiTester $I
     *
     * @return void
     */
    public function requestPatchCustomerFailsWhenPasswordsDoNotMatch(CustomerApiTester $I): void
    {
        $restCustomersAttributesTransfer = (new RestCustomersAttributesTransfer())
            ->setPassword('change123')
            ->setConfirmPassword('change1234');

        $I->sendPatch(
            $I->formatUrl(
                '{resourceCustomers}/{customerReference}',
                [
                    'resourceCustomers' => CustomersRestApiConfig::RESOURCE_CUSTOMERS,
                    'customerReference' => $this->customerTransfer->getCustomerReference(),
                ],
            ),
            [
                'data' => [
                    'type' => CustomersRestApiConfig::RESOURCE_CUSTOMERS,
                    'id' => $this->customerTransfer->getCustomerReference(),
                    'attributes' => $restCustomersAttributesTransfer->modifiedToArray(true, true),
                ],
            ],
        );

        // Assert
        $I->seeResponseCodeIs(Response::HTTP_UNPROCESSABLE_ENTITY);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesOpenApiSchema();

        $I->seeResponseErrorsHaveCode(CustomersRestApiConfig::RESPONSE_CODE_PASSWORDS_DONT_MATCH);
        $I->seeResponseErrorsHaveStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $I->seeResponseErrorsHaveDetail(sprintf(
            CustomersRestApiConfig::RESPONSE_DETAILS_PASSWORDS_DONT_MATCH,
            RestCustomersAttributesTransfer::PASSWORD,
            RestCustomersAttributesTransfer::CONFIRM_PASSWORD,
        ));
    }

    /**
     * @dataProvider requestPatchCustomerFailsValidationDataProvider
     *
     * @param \PyzTest\Glue\Customer\CustomerApiTester $I
     * @param \Codeception\Example $example
     *
     * @return void
     */
    public function requestPatchCustomerFailsValidation(CustomerApiTester $I, Example $example): void
    {
        if ($example['skip'] === true) {
            $I->markTestSkipped('This validation does not work for now.');
        }

        $I->sendPatch(
            $I->formatUrl(
                '{resourceCustomers}/{customerReference}',
                [
                    'resourceCustomers' => CustomersRestApiConfig::RESOURCE_CUSTOMERS,
                    'customerReference' => $this->customerTransfer->getCustomerReference(),
                ],
            ),
            [
                'data' => [
                    'type' => CustomersRestApiConfig::RESOURCE_CUSTOMERS,
                    'id' => $this->customerTransfer->getCustomerReference(),
                    'attributes' => $example['attributes'],
                ],
            ],
        );

        // Assert
        $I->seeResponseCodeIs($example[RestErrorMessageTransfer::STATUS]);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesOpenApiSchema();

        foreach ($example['errors'] as $index => $error) {
            $I->seeResponseErrorsHaveCode($error[RestErrorMessageTransfer::CODE], $index);
            $I->seeResponseErrorsHaveStatus($error[RestErrorMessageTransfer::STATUS], $index);
            $I->seeResponseErrorsHaveDetail($error[RestErrorMessageTransfer::DETAIL], $index);
        }
    }

    /**
     * @return array
     */
    protected function requestPatchCustomerFailsValidationDataProvider(): array
    {
        return [
            [
                'attributes' => [
                    RestCustomersAttributesTransfer::PASSWORD => 'qwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiop',
                    RestCustomersAttributesTransfer::CONFIRM_PASSWORD => 'qwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiop',
                ],
                RestErrorMessageTransfer::STATUS => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => [
                    [
                        RestErrorMessageTransfer::CODE => RestRequestValidatorConfig::RESPONSE_CODE_REQUEST_INVALID,
                        RestErrorMessageTransfer::STATUS => Response::HTTP_UNPROCESSABLE_ENTITY,
                        RestErrorMessageTransfer::DETAIL => 'password => This value is too long. It should have 64 characters or less.',
                    ],
                    [
                        RestErrorMessageTransfer::CODE => RestRequestValidatorConfig::RESPONSE_CODE_REQUEST_INVALID,
                        RestErrorMessageTransfer::STATUS => Response::HTTP_UNPROCESSABLE_ENTITY,
                        RestErrorMessageTransfer::DETAIL => 'confirmPassword => This value is too long. It should have 64 characters or less.',
                    ],
                ],
                'skip' => false,
            ],
            [
                'attributes' => [
                    RestCustomersAttributesTransfer::PASSWORD => 'qwe',
                    RestCustomersAttributesTransfer::CONFIRM_PASSWORD => 'qwe',
                ],
                RestErrorMessageTransfer::STATUS => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => [
                    [
                        RestErrorMessageTransfer::CODE => RestRequestValidatorConfig::RESPONSE_CODE_REQUEST_INVALID,
                        RestErrorMessageTransfer::STATUS => Response::HTTP_UNPROCESSABLE_ENTITY,
                        RestErrorMessageTransfer::DETAIL => 'password => This value is too short. It should have 8 characters or more.',
                    ],
                    [
                        RestErrorMessageTransfer::CODE => RestRequestValidatorConfig::RESPONSE_CODE_REQUEST_INVALID,
                        RestErrorMessageTransfer::STATUS => Response::HTTP_UNPROCESSABLE_ENTITY,
                        RestErrorMessageTransfer::DETAIL => 'confirmPassword => This value is too short. It should have 8 characters or more.',
                    ],
                ],
                'skip' => false,
            ],
            [
                'attributes' => [
                    RestCustomersAttributesTransfer::PASSWORD => 'qwertyui',
                    RestCustomersAttributesTransfer::CONFIRM_PASSWORD => 'qwertyui',
                ],
                RestErrorMessageTransfer::STATUS => Response::HTTP_BAD_REQUEST,
                'errors' => [
                    [
                        RestErrorMessageTransfer::CODE => CustomersRestApiConfig::RESPONSE_CODE_CUSTOMER_PASSWORD_INVALID_CHARACTER_SET,
                        RestErrorMessageTransfer::STATUS => Response::HTTP_BAD_REQUEST,
                        RestErrorMessageTransfer::DETAIL => CustomersRestApiConfig::RESPONSE_MESSAGE_CUSTOMER_PASSWORD_INVALID_CHARACTER_SET,
                    ],
                ],
                'skip' => true,
            ],
            [
                'attributes' => [
                    RestCustomersAttributesTransfer::PASSWORD => 'qwertyuI1!eee',
                    RestCustomersAttributesTransfer::CONFIRM_PASSWORD => 'qwertyuI1!eee',
                ],
                RestErrorMessageTransfer::STATUS => Response::HTTP_BAD_REQUEST,
                'errors' => [
                    [
                        RestErrorMessageTransfer::CODE => CustomersRestApiConfig::RESPONSE_CODE_CUSTOMER_PASSWORD_SEQUENCE_NOT_ALLOWED,
                        RestErrorMessageTransfer::STATUS => Response::HTTP_BAD_REQUEST,
                        RestErrorMessageTransfer::DETAIL => CustomersRestApiConfig::RESPONSE_MESSAGE_CUSTOMER_PASSWORD_SEQUENCE_NOT_ALLOWED,
                    ],
                ],
                'skip' => true,
            ],
        ];
    }
}
