#!/bin/bash
adb shell am start -a android.intent.action.SENDTO -d sms:01934333221 --es sms_body "SMS BODY GOES HERE" --ez exit_on_sent true
adb shell input keyevent 22
adb shell input keyevent 66