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
     * @var string
     */
    private $projectname;

    /**
     * @var string
     */
    private $taskname;

    /**
     * @var string
     */
    private $author;

    /**
     * Constructor
     *
     * @param string $author
     * @param string $projectname
     * @param string $taskname
     */
    public function __construct($author, $projectname, $taskname)
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
        $this->author = $author;
        $this->projectname = $projectname;
        $this->taskname = $taskname;
    }

    /**
     * Initiating monitoring record with returning unique id
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
                    'author' => $this->author,
                    'project' => $this->projectname,
                    'taskname' => $this->taskname,
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
            $this->client->get('comment', [
                'query' => [
                    'id' => $this->recordid,
                    'comment' => $comment,
                    'apikey' => $this->apikey
                ]
            ]);

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
            $this->client->get('finish', [
                'query' => [
                    'id' => $this->recordid,
                    'comment' => $comment,
                    'apikey' => $this->apikey
                ]
            ]);

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
            $this->client->get('fail', [
                'query' => [
                    'id' => $this->recordid,
                    'comment' => $comment,
                    'apikey' => $this->apikey
                ]
            ]);

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