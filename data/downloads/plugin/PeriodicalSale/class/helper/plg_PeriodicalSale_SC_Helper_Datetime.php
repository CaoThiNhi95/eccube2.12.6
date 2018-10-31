<?php

/*
 * PeriodicalSale
 * Copyright(c) 2015 DAISY Inc. All Rights Reserved.
 *
 * http://www.daisy.link/
 * 
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

require_once PLUGIN_UPLOAD_REALDIR . 'PeriodicalSale/PeriodicalSale.php';

/**
 * 日時操作のヘルパークラス
 *
 * @package PeriodicalSale
 * @author DAISY CO.,LTD.
 * @version $
 */
class plg_PeriodicalSale_SC_Helper_Datetime {

    /**
     * 次の特定の曜日の秒数を取得する。
     * 
     * @param integer $from_time 基準秒数
     * @param integer $day (date('w')の書式)
     * @param integer $offset_time 基準の秒数からのオフセット秒数
     * @return integer 秒数 
     */
    static function getTimeOfNextDay($from_time, $day, $offset_time = 0) {

        //基準秒数+オフセットの翌日から起算する
        $from_time += $offset_time + 60 * 60 * 24;
        //基準日時の詳細を取得
        $arrFrom = self::getDatetimeInfoFromTime($from_time);
        //基準日
        $value = mktime(0, 0, 0, $arrFrom['month'], $arrFrom['date'], $arrFrom['year']);

        //x曜日まで1日ずつ進める
        while (date('w', $value) != $day) {
            
            $value += 60 * 60 * 24;
        }
        return $value;
    }

    /**
     * 次々回の特定の曜日の秒数を取得する。
     * 
     * @param integer $from_time 基準秒数
     * @param integer $day (date('w')の書式)
     * @param integer $offset_time 基準の秒数からのオフセット秒数
     * @return integer 秒数 
     */
    static function getTimeOfDayAfterNext($from_time, $day, $offset_time = 0) {

        //次回の更に7日後の秒数を取得
        return self::getTimeOfNextDay($from_time, $day, $offset_time + 60 * 60 * 24 * 7);
    }

    /**
     * 基準の時刻から直近のx日の秒数を求める。
     * 指定日が基準月に存在しない場合、月の最終日を取得する。
     * 
     * @param integer $from_time 基準秒数
     * @param integer $date 日
     * @param integer $offset_time 基準の秒数からのオフセット秒数
     * @return integer 秒数 
     */
    static function getTimeOfNearestDate($from_time, $date, $offset_time = 0) {

        //基準秒数+オフセットの翌日から起算する
        $from_time += $offset_time + 60 * 60 * 24;
        //基準秒数の詳細を取得
        $arrFrom = self::getDatetimeInfoFromTime($from_time);
        //基準日+オフセットの翌日の、00:00:00
        $from_date_time = mktime(0, 0, 0, $arrFrom['month'], $arrFrom['date'], $arrFrom['year']);

        //基準日時の日付が存在しない場合
        if (!checkdate($arrFrom['month'], $date, $arrFrom['year'])) {

            //基準月の秒数を取得
            $first_date_time = mktime(0, 0, 0, $arrFrom['month'], 1, $arrFrom['year']);
            //基準月の最終日の秒数を取得
            $last_date_time = mktime(0, 0, 0, $arrFrom['month'], date('t', $first_date_time), $arrFrom['year']);

            //基準秒数が、基準月の最終日より後なら、
            if ($from_date_time > $last_date_time) {

                //翌月の秒数を取得
                $next_first_date_time = mktime(0, 0, 0, $arrFrom['month'] + 1, 1, $arrFrom['year']);
                //翌月の最終日の秒数を取得
                $value = mktime(0, 0, 0, $arrFrom['month'], date('t', $next_first_date_time), $arrFrom['year']);
            } 
            else {
                //基準月の最終日を代入
                $value = $last_date_time;
            }
        } 
        else {

            //基準秒数の年月のx日を取得
            $value = mktime(0, 0, 0, $arrFrom['month'], $date, $arrFrom['year']);

            //基準秒数が基準年月のx日を過ぎてしまっていたら
            if ($from_date_time > $value) {
                //翌月のx日を求める
                $value = mktime(0, 0, 0, $arrFrom['month'] + 1, $date, $arrFrom['year']);
            }
        }
        return $value;
    }

    /**
     * 基準の秒数から直近の第nx曜日の秒数を求める。
     * 
     * @param integer $from_time 基準秒数
     * @param integer $week 週番号
     * @param integer $day 曜日 (date('w')の書式)
     * @param integer $offset_time 基準の秒数からのオフセット秒数
     * @return integer 秒数 
     */
    static function getTimeOfNearestDayOfTheWeek($from_time, $week, $day, $offset_time = 0) {

        //基準秒数+オフセットの翌日から起算する
        $from_time += $offset_time + 60 * 60 * 24;
        //基準秒数の詳細を取得
        $arrFrom = self::getDatetimeInfoFromTime($from_time);
        //基準秒数の年月の第nx曜日を求める
        $value = self::getTimeOfDayOfTheWeek($arrFrom['year'], $arrFrom['month'], $week, $day);

        //基準秒数が第nx曜日を過ぎてしまっていたら
        if ($from_time > $value) {
            //翌月の第nx曜日を求める
            $value = self::getTimeOfDayOfTheWeek($arrFrom['year'], $arrFrom['month'] + 1, $week, $day);
        }
        return $value;
    }

    /**
     * 特定の年月の第nx曜日を求める。
     * 
     * @param integer $year 年
     * @param integer $month 月
     * @param integer $week 週番号
     * @param integer $day 曜日 (date('w')の書式)
     * @return integer 秒数
     */
    static function getTimeOfDayOfTheWeek($year, $month, $week, $day) {

        //指定した年月
        $value = mktime(0, 0, 0, $month, 1, $year);

        //第n週まで7日ずつ進める
        for ($i = 1; $i < $week; $i++) {
            $value += 60 * 60 * 24 * 7;
        }

        //x曜日まで1日ずつ進める
        while (date('w', $value) != $day) {
            $value += 60 * 60 * 24;
        }

        return $value;
    }

    /**
     * 渡された時間を要素別の配列にして返す。指定のない場合は現在時刻。
     * 
     * @param integer $time 時間
     * @return array 時間の要素別配列
     *  - year:  年
     *  - month: 月
     *  - date:  日
     *  - day:   曜日
     */
    static function getDatetimeInfoFromTime($time = null) {

        if (is_null($time)) {
            $time = time();
        }

        return array(
            'year' => date('Y', $time),
            'month' => date('n', $time),
            'date' => date('j', $time),
            'day' => date('w', $time)
        );
    }

    /**
     * 周期情報から、次回定期予定秒数を算出する。
     * 
     * @param array $arrPeriodInfo 周期情報
     *  - from_time     integer 基準秒数
     *  - period_type   string  周期タイプ (day|date)
     *  - period_date   integer 毎月○日 
     *  - period_week   integer 毎月第○週
     *  - period_day    integer ○曜日 (date('w')の書式)
     * @param integer $period_offset オフセット秒 (nullの場合プラグイン設定を優先)
     * @return integer 次回定期予定秒数
     */
    static function getNextPeriodTime(array $arrPeriodInfo, $period_offset = null) {

        //プラグインの命名済み情報を取得
        $arrNamedPluginInfo = PeriodicalSale::getNamedPluginInfo();
        $arrDefault = array(
            'from_time' => time(),
            'period_type' => PeriodicalSale::PERIOD_TYPE_MONTHLY_DATE,
            'period_date' => 1,
            'period_week' => 1,
            'period_day' => 0
        );
        //デフォルトの情報とマージ
        $arrPeriodInfo = array_merge($arrNamedPluginInfo, $arrDefault, $arrPeriodInfo);

        switch ($arrPeriodInfo['period_type']) {

            //毎週
            case PeriodicalSale::PERIOD_TYPE_WEEKLY:
                $period_offset = is_numeric($period_offset) ? $period_offset : $arrPeriodInfo['period_weekly_offset'];
                $next_period_time = self::getTimeOfNextDay($arrPeriodInfo['from_time'], $arrPeriodInfo['period_day'], $period_offset);
                break;

            //隔週
            case PeriodicalSale::PERIOD_TYPE_BIWEEKLY:
                $period_offset = is_numeric($period_offset) ? $period_offset : $arrPeriodInfo['period_biweekly_offset'];
                $next_period_time = self::getTimeOfDayAfterNext($arrPeriodInfo['from_time'], $arrPeriodInfo['period_day'], $period_offset);
                break;

            //毎月 (曜日指定)
            case PeriodicalSale::PERIOD_TYPE_MONTHLY_DAY:
                $period_offset = is_numeric($period_offset) ? $period_offset : $arrPeriodInfo['period_monthly_day_offset'];
                $next_period_time = self::getTimeOfNearestDayOfTheWeek($arrPeriodInfo['from_time'], $arrPeriodInfo['period_week'], $arrPeriodInfo['period_day'], $period_offset);
                break;

            //毎月 (日付指定)
            case PeriodicalSale::PERIOD_TYPE_MONTHLY_DATE:
                $period_offset = is_numeric($period_offset) ? $period_offset : $arrPeriodInfo['period_monthly_date_offset'];
                $next_period_time = self::getTimeOfNearestDate($arrPeriodInfo['from_time'], $arrPeriodInfo['period_date'], $period_offset);
                break;

            default:
                $next_period_time = 0;
                break;
        }
        return $next_period_time;
    }

}
