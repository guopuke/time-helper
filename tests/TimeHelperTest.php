<?php
/**
 * Author: Paco
 * Date: 2018/10/15 12:37
 */

namespace TimeHelper\Tests {

    use TimeHelper\TimeHelper;

    class TimeHelperTest extends \PHPUnit_Framework_TestCase
    {
        function testNow()
        {
            // 2018-10-15 04:39:04
            return TimeHelper::now();
        }
    }

}

