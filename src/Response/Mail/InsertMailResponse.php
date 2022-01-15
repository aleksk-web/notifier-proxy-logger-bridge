<?php

namespace App\Response\Mail;

use App\Exception\MalformedJsonException;
use App\Exception\MalformedResponseException;
use App\Response\BaseResponse;

/**
 * Class InsertMailResponse
 * @package App\Response\Mail
 */
class InsertMailResponse extends BaseResponse
{
    private const KEY_ID = "id";

    /**
     * @var int $id
     */
    private int $id;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Will prefill the base fields based on the data array from response
     *
     * @param string $json
     *
     * @return InsertMailResponse
     * @throws MalformedJsonException
     * @throws MalformedResponseException
     */
    public function prefillBaseFieldsFromJsonString(string $json): self
    {
        $dataArray    = $this->jsonToArray($json);
        $prefilledDto = parent::prefillBaseFieldsFromJsonString($json);

        $id = $this->getArrayValueByKey(self::KEY_ID, $dataArray);
        if( empty($id) ){
            throw new MalformedResponseException("Inserted E-Mail id is missing in response");
        }

        return $prefilledDto;
    }
}