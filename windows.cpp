#include <cstdio>
#include <cstring>
#include <ctime>
#include <iostream>
#include <cstdlib>
#include <windows.h>

using namespace std;

int main()
{
	char syscom[] = "php.exe exectbs.php > log.txt";
	struct tm *newtime;
	char tmpbuf[128];
	time_t lt1;
	int ly = 0, lm = 0, ld = 0, lh, lmin, ls;
	int isrun = 0;
	system(syscom);
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
			if (day != ld)
			{
				system(syscom);
			}
			else if (hour == 12 && lh != 12)
			{
				system(syscom);
			}
			else if (hour == 22 && lh != 22)
			{
				system(syscom);
			}
		}
		isrun = 1;
		ly = year,lm = month,ld = day,lmin = min,lh = hour,ls = sec;
		Sleep(100);
	}
	return 0;
}
