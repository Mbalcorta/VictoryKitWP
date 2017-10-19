<?
declare(strict_types=1);
define('ABSPATH', 1);

require_once(__DIR__. '/../actionkit.php');
require_once(__DIR__. '/../mailings.php');

function get_option($one){
}

function add_action($one, $two){
}

function getAll(){
  return array("location" => "blah");
}

function wp_remote_post($one, $two){
   return array('header'=> getAll(), 'response' => array('code' => ''));
}

class Event {

    private $observer;
    public function __construct($observer) {
        $this->observer = $observer;
    }

    public function doMagicAndNotify($params, $html) {
        // do heavy magic

        //notify observer
        $obsresult = $this->observer->request(array(
            'path' => 'mailer',
            'method' => 'post',
            'data' => array(
                'fromline' => "/rest/v1/fromline/{$params['from_line']}/",
                'subjects' => array($params['subject']),
                'notes' => 'Generated by VictoryKit',
                'emailwrapper' => 27, // Demand Progress wrapper
                'includes' => array(
                    'lists' => array(VK_LIST_ID), // VK list. TODO: store this as a constant somewhere
                    'users' => $params['subscribers'], // Subscribers
                ),
                'limit' => $params['limit'], // Limit users per mailing
                // 'excludes' => array(
                //     'mailings' => $mailings, // Array of mailing IDs, for avoiding multiple sends
                // ),
                'tags' => array('victorykit'),
                'html' => $html,
                'sort_by' => 'random',
            ),
        ));

        return 'didmagic';
    }
}


use PHPUnit\Framework\TestCase;

final class RequestMethodTest extends TestCase
  {
    //Testing mailings requestMail function is returning value from ActionKit request method
    public function testRequestMethodReturnsHeaders(): void
    {
      global $ak;
      $ak = $this->createMock(ActionKit::class);
      $ak->method('request')
         ->willReturn('');

     $mailings = new Mailings();
     $result = $mailings->requestMail(array('from_line' => '', 'subject' => '', 'subscribers' => '', 'limit' => ''), '');
     $this->assertEquals( $ak->request(''), $result);
    }


// set up observer on ActionKit class to be called once from mailings class with
//parameters
    public function testActionkitRequestParams()
    {
        $params = array('from_line'=>'', 'subject'=>'', 'subscribers' =>'', 'limit'=>'');
        $html = '';
        // Create a mock for the Observer class,
        // only mock the request() method.
        $observer = $this->getMockBuilder(ActionKit::class)
                         ->setMethods(['request'])
                         ->getMock();

        // Set up the expectation for the request() method
        // to be called only once and with the array of parameters
        // as its parameter.
        $observer->expects($this->once())
                 ->method('request')
                 ->with($this->equalTo(array(
                     'path' => 'mailer',
                     'method' => 'post',
                     'data' => array(
                         'fromline' => "/rest/v1/fromline/{$params['from_line']}/",
                         'subjects' => array($params['subject']),
                         'notes' => 'Generated by VictoryKit',
                         'emailwrapper' => 27, // Demand Progress wrapper
                         'includes' => array(
                             'lists' => array(VK_LIST_ID), // VK list. TODO: store this as a constant somewhere
                             'users' => $params['subscribers'], // Subscribers
                         ),
                         'limit' => $params['limit'], // Limit users per mailing
                         // 'excludes' => array(
                         //     'mailings' => $mailings, // Array of mailing IDs, for avoiding multiple sends
                         // ),
                         'tags' => array('victorykit'),
                         'html' => $html,
                         'sort_by' => 'random',
                     ),
                 )));

        // Create a Subject object and attach the mocked
        // Observer object to it.
        $event = new Event($observer);
        $returnValue = $event->doMagicAndNotify($params, $html);

        // Call the doSomething() method on the $subject object
        // which we expect to call the mocked Observer object's
        // update() method with the string 'something'.
        $this->assertEquals('didmagic', $returnValue) ;
    }

  }


