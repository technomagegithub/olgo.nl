/**
 * Apptha
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.apptha.com/LICENSE.txt
 *
 * ==============================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * ==============================================================
 * This package designed for Magento COMMUNITY edition
 * Apptha does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Apptha does not provide extension support in case of
 * incorrect edition usage.
 * ==============================================================
 *
 * @category    Apptha
 * @package     Apptha_PriceCount
 * @version     0.1.4
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2014 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 *
 */

 var timer =1;
        if (typeof(BackColor)=="undefined")
            BackColor = "white";
        if (typeof(ForeColor)=="undefined")
            ForeColor= "black";
        if (typeof(DisplayFormat)=="undefined")
            DisplayFormat = "<div class='count-date'><span class='value-date day'>%%D%%</span><span class='format-date'>Days</span></div><div class='count-date'><span class='value-date hour'>%%H%%</span><span class='format-date'>Hours</span></div><div class='count-date'><span class='value-date min'>%%M%%</span><span class='format-date'>Minutes</span></div><div class='count-date'><span class='value-date sec'>%%S%%</span><span class='format-date'>Seconds</span></div>";
        if (typeof(CountActive)=="undefined")
            CountActive = true;
        if (typeof(FinishMessage)=="undefined")
            FinishMessage = "";
        if (typeof(CountStepper)!="number")
            CountStepper = -1;
        if (typeof(LeadingZero)=="undefined")
            LeadingZero = true;
        CountStepper = Math.ceil(CountStepper);
        if (CountStepper == 0)
            CountActive = false;
        var SetTimeOutPeriod = (Math.abs(CountStepper)-1)*1000 + 990;
        function calcage(secs, num1, num2) {
            s = ((Math.floor(secs/num1)%num2)).toString();
            if (LeadingZero && s.length < 2)
                s = "0" + s;
            return "<b>" + s + "</b>";
        }
        function CountBack(secs,iid,timer) {
            if (secs < 0) {
                document.getElementById(iid).innerHTML = FinishMessage;
                document.getElementById('caption'+timer).style.display = "none";
                document.getElementById('heading'+timer).style.display = "none";
                return;
            }
            DisplayStr = DisplayFormat.replace(/%%D%%/g, calcage(secs,86400,100000));
            DisplayStr = DisplayStr.replace(/%%H%%/g, calcage(secs,3600,24));
            DisplayStr = DisplayStr.replace(/%%M%%/g, calcage(secs,60,60));
            DisplayStr = DisplayStr.replace(/%%S%%/g, calcage(secs,1,60));
            document.getElementById(iid).innerHTML = DisplayStr;
            if (CountActive)
                setTimeout(function(){CountBack((secs+CountStepper),iid,timer)}, SetTimeOutPeriod);
        }
