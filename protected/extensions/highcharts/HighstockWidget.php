<?php

/**
 * HighstockWidget class file.
 *
 * @author Milo Schuman <miloschuman@gmail.com>
 * @link https://github.com/miloschuman/yii-highcharts/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @version 3.0.5
 */

Yii::import('ext.highcharts.HighchartsWidget');

/**
 * @see HighchartsWidget
 */
class HighstockWidget extends HighchartsWidget
{

	protected $_constr = 'StockChart';
	protected $_baseScript = 'highstock';

}