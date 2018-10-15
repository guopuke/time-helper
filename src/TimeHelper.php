<?php
/**
 * @author: Paco <guopuke@gmail.com>
 * @date: 2018-10-15 11:49:03
 */

namespace TimeHelper;


final class TimeHelper
{
    const DAY_SECONDS = 24 * 60 * 60;
    const HOURS_SECONDS = 60 * 60;
    const WEEK_SECONDS = 7 * 24 * 60 * 60;

    /**
     * 获取当前时间
     *
     * @param string $format
     * @return false|string
     */
    public static function now($format = 'Y-m-d H:i:s')
    {
        return date($format);
    }

    /**
     * 获取今天开始时间
     *
     * @param int|null $time 默认为当前时间戳
     * @return false|int
     */
    public static function getTodayStart(int $time = null)
    {
        $time = $time ?? time();

        return mktime(0, 0, 0, date("m", $time), date("d", $time), date("Y", $time));
    }

    /**
     * 获取今天结束时间
     *
     * @param int|null $time 默认为当前时间戳
     * @return false|int
     */
    public static function getTodayEnd(int $time = null)
    {
        $time = $time ?? time();

        return mktime(23, 59, 59, date("m", $time), date("d", $time), date("Y", $time));
    }

    /**
     * 获取今天还剩余多少秒
     *
     * @return false|int
     */
    public static function getTodaySurplus()
    {
        $time = time();
        $surplus = self::getTodayEnd($time) - $time;

        return ($surplus > 0) ? $surplus : 1;
    }

    /**
     * 将秒数转换为「天小时分秒」格式
     *
     * @param $time
     * @return string
     */
    public static function translateSecs(int $time)
    {
        $output = '';
        foreach (array(86400 => '天', 3600 => '小时', 60 => '分', 1 => '秒') as $key => $value) {
            if ($time >= $key) $output .= floor($time / $key) . $value;
            $time %= $key;
        }
        return $output;
    }

    /**
     * 根据开始时分、结束时分与当前时间判断实际起始日期（计算给定时分的跨天时间）
     * ex:
     *  $start_time = "20:00:00";
     *  $end_time = "06:00:00";
     *
     * This data can be obtained:
     *  array:2 [
     *      0 => 1513166400
     *      1 => 1513198800
     *  ]
     *
     * If the current time is not within two time points, it will return to the empty array
     *
     *
     * @param string      $start_time
     * @param string      $end_time
     * @param string|null $current_time
     * @return array
     */
    public static function interval(string $start_time, string $end_time, string $current_time = null)
    {
        $current_time = $current_time ?? date('H:i:s');

        if ($start_time > $end_time) {
            if ($current_time > $start_time && $current_time > $end_time) {
                // dump('今天开始,明天结束');
                $start = strtotime($start_time);
                $end = strtotime("$end_time +1 day");
            } elseif ($current_time < $end_time && $current_time < $start_time) {
                // dump('昨天开始,今天结束');
                $start = strtotime("$start_time -1 day");
                $end = strtotime($end_time);
            }
        } else {
            if ($current_time > $start_time && $current_time < $end_time) {
                // dump('今天开始,今天结束');
                $start = strtotime($start_time);
                $end = strtotime($end_time);
            }
        }

        if (isset($start) && isset($end)) {
            $result = array($start, $end);
        }

        return $result ?? array();
    }


    /**
     * 计算距离下次时间为偶数的剩余多少秒
     *
     * @return int
     */
    public static function oddRemainSeconds(): int
    {
        $now_time = time();
        $h = date('H');
        $is_odd = $h % 2;
        if ($is_odd !== 1) {
            $real_time = strtotime('+1 hours');
            $real_time = date('Y-m-d H', $real_time);
            $real_time = $real_time . ':59:59';
        } else {
            $real_time = date('Y-m-d H:') . '59:59';
        }
        $result = strtotime($real_time) - $now_time;
        if (!is_numeric($result)) {
            return 1;
        } else {
            return ($result > 0) ? $result : 1;
        }
    }

    /**
     * 获取指定前某月份的第一天与最后一天
     * ex: [ "2018-09-01 00:00:00", "2018-09-30 23:59:59" ]
     *
     * @param int $month_ago
     * @return array
     */
    public static function firstAndLastDayOfMonth(int $month_ago)
    {
        return [
            date('Y-m-d 00:00:00', strtotime("first day of -{$month_ago} month")),
            date('Y-m-d 23:59:59', strtotime("last day of -{$month_ago} month")),
        ];
    }

    /**
     * 根据开启时间及关闭时间, 自动计算离合法时间剩余多少秒, 自动顺延至第二天
     *
     * @param int $allow_start_hour
     * @param int $allow_end_hour
     * @return int
     */
    public static function allowSecondsLater(int $allow_start_hour, int $allow_end_hour): int
    {
        $start_time = strtotime(date($allow_start_hour . ':00:00'));
        $end_time = strtotime(date($allow_end_hour . ':59:59'));
        $now_time = time();
        if ($now_time < $start_time) {
            return $start_time - $now_time;
        } elseif ($now_time > $end_time) {
            return strtotime('+1 days', $start_time) - $now_time;
        } else {
            return 1;
        }
    }

}
