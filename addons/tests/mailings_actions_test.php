<?
declare(strict_types=1);
define('ABSPATH', 1);

require_once(__DIR__. '/../actionkit.php');
require_once(__DIR__. '/../mailings.php');

function get_option(){}
function add_action(){}
function update_option(){}

class wordPress {
  function query(){}
};

use PHPUnit\Framework\TestCase;

final class RequestMethodTest extends TestCase
  {
    public function testVkMailingsUpdateSubscribedCallsActionkitQuery(): void
    {
      global $ak;
      $ak = $this->createMock(ActionKit::class);
      $ak->expects($this->once())
         ->method('query');

      vk_mailings_update_subscribed_users_count_action($ak);
    }

    public function testVkMailingsUpdateSubscribedReturnValueZero(): void
     {
       global $ak;
       $ak = $this->createMock(ActionKit::class);
       $ak->method('query')
          ->willReturn(array('success' => null));

      $result = vk_mailings_update_subscribed_users_count_action($ak);
      $this->assertEquals( 0, $result);
     }

    //  public function testVkMailingsUpdateSubscribedReturnValueFour(): void
    //   {
    //     global $ak;
    //     $ak = $this->createMock(ActionKit::class);
    //     $ak->method('query')
    //        ->willReturn(array('success' => true, 'data' => array('user_count' => 4)));
     //
    //    $result = vk_mailings_update_subscribed_users_count_action($ak);
    //    $this->assertEquals( 4, $result);
    //   }
    //   public function testUpdateOptionFunction(): void
    //    {
    //     $result = updateOption(0);
    //     $this->assertEquals( 4, $result);
    //    }
    public function testVkMailingsSyncSubscribersAction(): void
     {
       global $ak;
       global $wpdb;
       $wpdb = new wordPress;

       $ak = $this->createMock(ActionKit::class);
       $ak->method('query')
          ->willReturn(array('data'=>array(array('user_id' => 4), array('user_id' => 5), array('user_id' => 6))));

      $result = vk_mailings_sync_subscribers_action($ak);
      $this->assertEquals( array(0 => array( 0 => 4, 1 => 5, 2 => 6)), $result);
     }
   public function testInsertSubscriberTodb(): void
    {
      global $wpdb;
      $wpdb = new wordPress;
      $id_chunks = array(0 => array( 0 => 4, 1 => 5, 2 => 6));

     $result = insertSubscriberTodb($id_chunks, $wpdb);
     $this->assertEquals( array('results' => '(4), (5), (6)'), $result);
    }
  }