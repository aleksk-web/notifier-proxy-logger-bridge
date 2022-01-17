<?php


namespace App;


use App\DTO\Mail\MailDTO;
use App\Request\BaseRequest;
use App\Request\Discord\InsertDiscordMessageRequest;
use App\Request\Mail\GetMailStatusRequest;
use App\Request\Mail\InsertMailRequest;
use App\Response\BaseResponse;
use App\Response\Discord\InsertDiscordMessageResponse;
use App\Response\Mail\GetMailStatusResponse;
use App\Response\Mail\InsertMailResponse;
use App\Service\GuzzleHttpService;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use LogicException;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Throwable;
use TypeError;

class NotifierProxyLoggerBridge
{

    const WEBHOOK_NAME_ALL_NOTIFICATIONS = "allNotifications";
    const SOURCE_PMS                     = "PMS";
    const SOURCE_CIR                     = "CIR";

    /**
     * @var GuzzleHttpService $guzzleHttpService
     */
    private GuzzleHttpService $guzzleHttpService;

    /**
     * @var string $baseUrl
     */
    private string $baseUrl;

    /**
     * @var Logger $logger
     */
    private Logger $logger;

    public function __construct(string $logFilePath, string $loggerName, string $baseUrl)
    {
        $this->baseUrl           = $baseUrl;
        $this->guzzleHttpService = new GuzzleHttpService();
        $this->logger            = new Logger($loggerName);
        $this->logger->pushHandler(new StreamHandler($logFilePath, Logger::DEBUG));
    }

    /**
     * Will call the NPL to insert the discord message
     *
     * @param InsertDiscordMessageRequest $request
     * @return InsertDiscordMessageResponse
     * @throws GuzzleException
     */
    public function insertDiscordMessage(InsertDiscordMessageRequest $request): InsertDiscordMessageResponse
    {
        $response = new InsertDiscordMessageResponse();
        try{
            $this->logCalledApiMethod($request);
            {
                $absoluteCalledUrl = $this->buildAbsoluteCalledUrlForRequest($request);
                $guzzleResponse    = $this->guzzleHttpService->sendPostRequest($absoluteCalledUrl, $request->toArray());

                $response->prefillBaseFieldsFromJsonString($guzzleResponse);
            }
            $this->logResponse($response);
        }catch(Exception | TypeError $e){
            $this->logThrowable($e);
            return $response->prefillInternalBridgeError();
        }

        return $response;
    }

    /**
     * Will call the NPL to insert the mail
     *
     * @param InsertMailRequest $request
     * @return InsertMailResponse
     * @throws GuzzleException
     */
    public function insertMail(InsertMailRequest $request): InsertMailResponse
    {
        $response = new InsertMailResponse();
        try{
            $attachedFilesNames = [];
            foreach($request->getMailDto()->getAttachments() as $fileName => $fileContent){
                if( in_array($fileName, $attachedFilesNames) ){
                    throw new LogicException("Attachments names must be unique. This one is not unique: {$fileName}");
                }
                $attachedFilesNames[] = $fileName;
            }

            if( !in_array($request->getMailDto()->getEmailType(), MailDTO::ALLOWED_TYPES) ){
                throw new LogicException("Provided E-Mail type is not supported: {$request->getMailDto()->getEmailType()}. Allowed are:" . json_encode(MailDTO::ALLOWED_TYPES));
            }

            $this->logCalledApiMethod($request);
            {
                $absoluteCalledUrl = $this->buildAbsoluteCalledUrlForRequest($request);
                $guzzleResponse    = $this->guzzleHttpService->sendPostRequest($absoluteCalledUrl, $request->toArray());

                $response->prefillBaseFieldsFromJsonString($guzzleResponse);
            }
            $this->logResponse($response);
        }catch(Exception | TypeError $e){
            $this->logThrowable($e);
            return $response->prefillInternalBridgeError();
        }

        return $response;
    }

    /**
     * Will call the NPL to get mail status
     *
     * @param GetMailStatusRequest $request
     * @return GetMailStatusResponse
     * @throws GuzzleException
     */
    public function getMailStatus(GetMailStatusRequest $request): GetMailStatusResponse
    {
        $response = new GetMailStatusResponse();
        try{
            $this->logCalledApiMethod($request);
            {
                $absoluteCalledUrl = $this->buildAbsoluteCalledUrlForRequest($request);
                $guzzleResponse    = $this->guzzleHttpService->sendGetRequest($absoluteCalledUrl);

                $response->prefillBaseFieldsFromJsonString($guzzleResponse);
            }
            $this->logResponse($response);
        }catch(Exception | TypeError $e){
            $this->logThrowable($e);
            return $response->prefillInternalBridgeError();
        }

        return $response;
    }

    /**
     * Will return the absolute url to be called by guzzle
     *
     * @param BaseRequest $request
     * @return string
     */
    public function buildAbsoluteCalledUrlForRequest(BaseRequest $request): string
    {
        if( substr($this->baseUrl, -1) === DIRECTORY_SEPARATOR ){
            $this->baseUrl = substr($this->baseUrl, 0, strlen($this->baseUrl) -1);
        }

        return $this->baseUrl . $request->getRequestUri();
    }

    /**
     * @param Throwable $e
     */
    private function logThrowable(Throwable $e): void
    {

        $this->logger->critical("Exception was thrown", [
            "message" => $e->getMessage(),
            "code"    => $e->getCode(),
            "trace"   => $e->getTraceAsString(),
        ]);
    }

    /**
     * Will log information about current api call
     *
     * @param BaseRequest $request
     */
    private function logCalledApiMethod(BaseRequest $request): void
    {
        $this->logger->info("Now calling api: ", [
            "calledMethod"  => debug_backtrace()[1]['function'], // need to use backtrace to get the correct calling method
            "baseUrl"       => $this->baseUrl,
            "requestUri"    => $request->getRequestUri(),
            "dataBag"       => $request->toArray(),
        ]);
    }

    /**
     * Will log the response data
     *
     * @param BaseResponse $response
     */
    private function logResponse(BaseResponse $response): void
    {
        $this->logger->info("Got response from called endpoint", [
            "response" => $response->toJson(),
        ]);
    }

}