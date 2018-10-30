<?php
/**
 * Created by PhpStorm.
 * User: igor
 * Date: 10/9/17
 * Time: 2:27 PM
 */

namespace Monitoring\Smith;

use Dotenv\Dotenv;

/**
 * Class Client
 * @package monitoring/smithclient
 */
class Client
{
    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * API_KEY
     *
     * @var string
     */
    protected $apikey;

    /**
     * @var integer
     */
    private $recordid = null;

    /**
     * Constructor
     *
     * @param string $projectname
     * @param string $name
     */
    public function __construct($projectname, $name)
    {
        // Load configuration file .env
        if(!empty($_SERVER['DOCUMENT_ROOT']))
        {
            $dotenv = new Dotenv($_SERVER['DOCUMENT_ROOT']);
        }
        else
        {
            // Used for laravel projects
            $dotenv = new Dotenv(getcwd());
        }
        $dotenv->load();

        // Configure HTTP Client
        $this->client = new \GuzzleHttp\Client([
            'base_uri' => getenv('SMITH_API_URL'),
            'timeout' => 30,
            'verify' => false
        ]);

        // Set apikey token
        $this->apikey = getenv('SMITH_API_KEY');

        // Set projectname, name
        $this->projectname = $projectname;
        $this->name = $name;
    }

    /**
     * Initiating test record with returning unique id
     *
     * @param int|string $expectedtime
     * @return bool|int
     */
    public function start($expectedtime)
    {
        try
        {
            $response = $this->client->get('start', [
                'query' => [
                    'project' => $this->projectname,
                    'name' => $this->name,
                    'expected_time' => $expectedtime,
                    'apikey' => $this->apikey
                ]
            ]);

            $returnobject = json_decode($response->getBody()->getContents());

            $this->recordid = $returnobject->id;

            return true;
        }
        catch(\Exception $e)
        {
            return false;
        }
    }

    /**
     * Update comment of the record
     *
     * @param string $comment
     * @return bool|mixed
     */
    public function comment($comment)
    {
        try
        {
            $response = $this->client->get('comment', [
                'query' => [
                    'id' => $this->recordid,
                    'comment' => $comment,
                    'apikey' => $this->apikey
                ]
            ]);

            $returnobject = json_decode($response->getBody()->getContents());

            return true;
        }
        catch(\Exception $e)
        {
            return false;
        }
    }

    /**
     * Set record as finished
     *
     * @param string|null $comment
     * @return bool|mixed
     */
    public function finish($comment = null)
    {
        try
        {
            $response = $this->client->get('finish', [
                'query' => [
                    'id' => $this->recordid,
                    'comment' => $comment,
                    'apikey' => $this->apikey
                ]
            ]);

            $returnobject = json_decode($response->getBody()->getContents());

            return true;
        }
        catch(\Exception $e)
        {
            return false;
        }
    }

    /**
     * Set record as failed
     *
     * @param string|null $comment
     * @return bool|mixed
     */
    public function fail($comment = null)
    {
        try
        {
            $response = $this->client->get('fail', [
                'query' => [
                    'id' => $this->recordid,
                    'comment' => $comment,
                    'apikey' => $this->apikey
                ]
            ]);

            $returnobject = json_decode($response->getBody()->getContents());

            return true;
        }
        catch(\Exception $e)
        {
            return false;
        }
    }

    /**
     * Get current recordid
     *
     * @return int
     */
    public function getRecordId()
    {
        return $this->recordid;
    }
}