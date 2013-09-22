#include <cstdio>
#include <cstring>
#include <ctime>
#include <iostream>
#include <cstdlib>
#include <windows.h>

using namespace std;

int main()
{
	char syscom[] = "php exectbs.php > log/log.%d-%d-%d-%d.txt";
	char syscac[] = "php exectbs.php cachetb > log/log.%d-%d-%d.txt";
	system("md log");
	struct tm *newtime;
	char tmpbuf[128];
	time_t lt1;
	int ly = 0, lm = 0, ld = 0, lh, lmin, ls;
	int isrun = 0;
	time(&lt1);
	newtime = localtime(&lt1);
	strftime(tmpbuf, 128, "%Y", newtime);
	int year = atoi(tmpbuf);
	strftime(tmpbuf, 128, "%d", newtime);
	int day=atoi(tmpbuf);
	strftime(tmpbuf, 128, "%m", newtime);
	int month = atoi(tmpbuf);
	strftime(tmpbuf, 128, "%H", newtime);
	int hour = atoi(tmpbuf);
	strftime(tmpbuf, 128, "%M", newtime);
	int min = atoi(tmpbuf);
	strftime(tmpbuf, 128, "%S", newtime);
	int sec = atoi(tmpbuf);
	sprintf(tmpbuf,syscac,year,month,day);
	system(tmpbuf);
	sprintf(tmpbuf,syscom,year,month,day,hour);
	system(tmpbuf);	
	while (1)
	{
		time(&lt1);
		newtime = localtime(&lt1);
		strftime(tmpbuf, 128, "%Y", newtime);
		int year = atoi(tmpbuf);
		strftime(tmpbuf, 128, "%d", newtime);
		int day=atoi(tmpbuf);
		strftime(tmpbuf, 128, "%m", newtime);
		int month = atoi(tmpbuf);
		strftime(tmpbuf, 128, "%H", newtime);
		int hour = atoi(tmpbuf);
		strftime(tmpbuf, 128, "%M", newtime);
		int min = atoi(tmpbuf);
		strftime(tmpbuf, 128, "%S", newtime);
		int sec = atoi(tmpbuf);
		if (isrun)
		{
			/*
			if (day != ld)
			{
				system(syscom);
			}
			else if (hour == 2 && lh != 2)
			{
				system(syscom);
			}
			else if (hour == 7 && lh != 7)
			{
				system(syscom);
			}
			else if (hour == 12 && lh != 12)
			{
				system(syscom);
			}
			else if (hour == 18 && lh != 18)
			{
				system(syscom);
			}
			else if (hour == 23 && lh != 23)
			{
				system(syscom);
			}
			*/
			if (hour==23 && lh != 23)
			{
				sprintf(tmpbuf,syscac,year,day,month);
				system(tmpbuf);
			}
			if (min == 0 && lm != 0)
			{
				sprintf(tmpbuf,syscom,year,day,month,hour);
				system(tmpbuf);
			}
		}
		isrun = 1;
		ly = year,lm = month,ld = day,lmin = min,lh = hour,ls = sec;
		Sleep(100);
	}
	return 0;
}
