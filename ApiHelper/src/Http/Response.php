<?php

namespace ApiHelper\Http;

use League\Fractal\Manager;
use League\Fractal\Serializer\SerializerAbstract;
use League\Fractal\TransformerAbstract;
use League\Fractal\Resource\ResourceAbstract;
use ApiHelper\Resource\CraftItem;
use ApiHelper\Resource\CraftCollection;
use ApiHelper\Pagination\CraftPaginateVariableAdapter;
use ApiHelper\Transformers\ErrorTransformer;
use Craft\BaseElementModel;
use Craft\ElementCriteriaModel;
use ApiHelper\Validation\Exceptions\ApiHelperException;
use Streamer\Stream as Streamer;
use \ApiHelper\Http\Psr7\Stream;

class Response extends \ApiHelper\Http\Psr7\Response
{
    /**
     * Request
     *
     * @var ApiHelper\Http\Request
     */
    protected $request;

    /**
     * Manager
     *
     * @var League\Fractal\Manager
     */
    protected $manager;

    /**
     * Serializer
     *
     * @var League\Fractal\Serializer\DataArraySerializer
     */
    protected $serializer;

    /**
     * Transformer
     *
     * @var League\Fractal\TransformerAbstract
     */
    protected $transformer;

    /**
     * Error Transformer
     *
     * @var League\Fractal\TransformerAbstract
     */
    protected $error_transformer;

    /**
     * Item
     *
     * @var array
     */
    protected $item;

    /**
     * Collection
     *
     * @var array
     */
    protected $collection;

    /**
     * Paginator
     *
     * @var ApiHelper\Pagination\CraftPaginateVariableAdapter
     */
    protected $paginator;

    /**
     * Meta
     *
     * @var array
     */
    protected $meta = [];

    /**
     * Temp
     *
     * @var array
     */
    protected $temp = null;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct(Request $request = null)
    {
        $this->request = $request;
        $this->manager = new Manager();

        // Message
        $this->setDefaultProtocolVersion();
        $this->setDefaultHeaders();
        $this->setDefaultBody();

        // Response
        $this->setDefaultStatusCode();
        $this->setDefaultReasonPhrase();

        // Other
        if ($request) {
            $this->setDefaultTransformer();
        }

        $this->setDefaultSerializer();
        $this->setDefaultErrorTransformer();
    }

    /**
     * Set Default Protocol Version
     *
     * @return void
     */
    protected function setDefaultProtocolVersion()
    {
        $this->protocol_version = \Craft\craft()->request->getHttpVersion();
    }

    /**
     * Set Default Headers
     *
     * @return void
     */
    protected function setDefaultHeaders()
    {
        $this->headers = [
            'Pragma'        => [
                'no-cache',
            ],
            'Cache-Control' => [
                'no-store',
                'no-cache',
                'must-revalidate',
                'post-check=0',
                'pre-check=0',
            ],
            'Content-Type' => [
                'application/json; charset=utf-8',
            ],
        ];
    }

    /**
     * Set Default Body
     *
     * @return void
     */
    protected function setDefaultBody()
    {
        $streamer = new Streamer(fopen('php://temp', 'w+'));
        $this->body = new Stream($streamer);
    }

    /**
     * Set Default Status Code
     *
     * @return void
     */
    protected function setDefaultStatusCode()
    {
        $this->status_code = 200;
    }

    /**
     * Set Default Reason Phrase
     *
     * @return void
     */
    protected function setDefaultReasonPhrase()
    {
        $this->reason_phrase = 'OK';
    }

    /**
     * Set Serializer
     *
     * @param SerializerAbstract $serializer Serializer
     *
     * @return Response Response
     */
    public function setSerializer(SerializerAbstract $serializer)
    {
        $this->serializer = $serializer;

        return $this;
    }

    /**
     * Set Default Serializer
     *
     * @return void
     */
    private function setDefaultSerializer()
    {
        $serializerKey = \Craft\craft()->config->get('defaultSerializer', 'apiHelper');
        $serializer = \Craft\craft()->config->get('serializers', 'apiHelper')[$serializerKey];

        $this->serializer = new $serializer;
    }

    /**
     * Get Serializer
     *
     * @return SerializerAbstract Serializer
     */
    public function getSerializer()
    {
        return $this->serializer;
    }

    /**
     * Set Transformer
     *
     * @param TransformerAbstract $transformer Transformer
     *
     * @return Response Response
     */
    public function setTransformer(TransformerAbstract $transformer)
    {
        $this->transformer = $transformer;

        return $this;
    }

    /**
     * Set Default Transformer
     *
     * @return TransformerAbstract Transformer
     */
    private function setDefaultTransformer()
    {
        $element_type = $this->request->getAttribute('elementType');

        $transformer = sprintf('ApiHelper\\Transformers\\%sTransformer', $element_type);

        $this->transformer = new $transformer;
    }

    /**
     * Get Transformer
     *
     * @return TransformerAbstract Transformer
     */
    public function getTransformer()
    {
        return $this->transformer;
    }

    /**
     * Set Error Transformer
     *
     * @param TransformerAbstract $transformer Transformer
     *
     * @return Response Response
     */
    public function setErrorTransformer(TransformerAbstract $transformer)
    {
        $this->error_transformer = $transformer;

        return $this;
    }

    /**
     * Set Default Error Transformer
     *
     * @return TransformerAbstract Transformer
     */
    private function setDefaultErrorTransformer()
    {
        $this->error_transformer = new ErrorTransformer;
    }

    /**
     * Get Error Transformer
     *
     * @return TransformerAbstract Error Transformer
     */
    public function getErrorTransformer()
    {
        return $this->error_transformer;
    }

    /**
     * Set Headers
     *
     * @param array $headers Headers
     *
     * @return Response Response
     */
    public function setHeaders(array $headers)
    {
        HeaderHelper::setNoCache();
        HeaderHelper::setContentTypeByExtension('json');

        return $this;
    }

    /**
     * Get Headers
     *
     * @return array Headers
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Add Header
     *
     * @param string $key   Key
     * @param string $value Value
     *
     * @return Response Response
     */
    public function addHeader($key, $value)
    {
        if ($key) {
            $this->headers[$key] = $value;
        } else {
            $this->headers[] = $value;
        }

        return $this;
    }

    /**
     * Set Status
     *
     * @param int $http_status_code Http Status Code
     *
     * @return Response Response
     */
    public function setStatus($status, $message = '')
    {
        $response = $this->withStatus($status, $message);

        $this->status_code = $response->status_code;
        $this->reason_phrase = $response->reason_phrase;

        return $this;
    }

    /**
     * Set Item
     *
     * @param mixed $item Item
     *
     * @return Response Response
     */
    public function setItem($item)
    {
        $this->item = $item;

        return $this;
    }

    /**
     * Set Collection
     *
     * @param array $items Items
     *
     * @return Response Response
     */
    public function setCollection($items)
    {
        $this->collection = $items;

        return $this;
    }

    /**
     * Set Paginated Collection
     *
     * @param array $items Items
     *
     * @return Response Response
     */
    public function setPaginatedCollection(array $items)
    {
        $this->setCollection($items);

        $this->paginator = new CraftPaginateVariableAdapter($this->request->getCriteria());

        return $this;
    }

    /**
     * Set Created
     *
     * @param BaseElementModel|null $model Model
     *
     * @return Response Response
     */
    public function setCreated($location = null)
    {
        $this->setStatus(201);

        if ($location) {
            $response = $response->addWithHeader('Location', $location);

            $this->headers = $response->headers;
        }

        return $this;
    }

    /**
     * Set Error
     *
     * @param ApiHelperException $exception Exception
     *
     * @return Response Response
     */
    public function setError(ApiHelperException $exception)
    {
        $body = [
            'error' => [
                'message' => $exception->getMessage(),
            ],
        ];

        if ($exception->hasErrors()) {
            $body['error']['errors'] = $exception->getErrors();
        }

        if ($exception->hasInput()) {
            $body['error']['input'] = $exception->getInput();
        }

        $body['error']['debug'] = $exception->getTrace();

        $this->transformer = $this->error_transformer;

        $this->item = [$body];

        return $this;
    }

    /**
     * Set body
     *
     * @param mixed $body Body
     *
     * @return Response Response
     */
    public function setBody($body)
    {

        $streamer = new Streamer(fopen('php://input', 'r'));
        $this->body = new Stream($streamer);

        return $this;
    }

    /**
     * Set Meta
     *
     * @param array $meta Meta
     *
     * @return Response Response
     */
    public function setMeta(array $meta)
    {
        $this->meta = $meta;

        return $this;
    }

    /**
     * Get Meta
     *
     * @return array Meta
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * Add Meta
     *
     * @param string $key   Key
     * @param mixed  $value Value
     *
     * @return Response Response
     */
    public function addMeta($key, $value)
    {
        $this->meta[$key] = $value;

        return $this;
    }

    /**
     * Send
     *
     * @return void
     */
    public function send()
    {
        $this->applyFractal();

        $this->applyMeta();

        $this->applyStatus();

        $this->applyHeaders();

        $this->applyBody();

        ob_start();

        echo $this->getBody()->getContents();

        \Craft\craft()->end();
    }

    /**
     * Apply Status
     *
     * @return void
     */
    private function applyStatus()
    {
        $header = [
            null,
            sprintf('HTTP/%s %d %s', $this->getProtocolVersion(), $this->getStatusCode(), $this->getReasonPhrase())
        ];

        \Craft\HeaderHelper::setHeader($header);
    }

    /**
     * Apply Headers
     *
     * @return void
     */
    private function applyHeaders()
    {
        $headers = [];

        foreach ($this->headers as $header => $values) {
            $headers[$header] = implode(', ', $values);
        }

        \Craft\HeaderHelper::setHeader($headers);
    }

    /**
     * Apply Body
     *
     * @return void
     */
    private function applyBody()
    {
        if ($this->temp) {
            $body = \Craft\JsonHelper::encode($this->temp);

            $this->body->write($body);

            $this->body->rewind();
        }
    }

    /**
     * Apply Fractal
     *
     * @return void
     */
    private function applyFractal()
    {
        if ($this->item) {
            $body = new CraftItem($this->item, $this->transformer);
        }

        if ($this->collection) {
            $body = new CraftCollection($this->collection, $this->transformer);

            if ($this->paginator) {
                $body->setPaginator($this->paginator);
            }
        }

        if (isset($body)) {
            $this->manager->setSerializer($this->serializer);

            $this->temp = $this->manager->createData($body)->toArray();
        }
    }

    /**
     * Apply Meta
     *
     * @return void
     */
    private function applyMeta()
    {
        if (isset($this->temp['meta'])) {
            $this->temp['meta'] = array_merge($this->temp['meta'], $this->meta);
        }
    }
}
