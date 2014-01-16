#include <stdio.h>

const char *CreateV2Key(
	int level, 
	char *encryption_template, 
	char *name_to_make_key_for, 
	unsigned long hardwareID, 
	unsigned short otherinfo1, 
	unsigned short otherinfo2, 
	unsigned short otherinfo3, 
	unsigned short otherinfo4, 
	unsigned short otherinfo5);
	
const char *CreateShortV3Key(
	int level, 
	char *encryption_template, 
	char *name_to_make_key_for, 
	unsigned long hardwareID, 
	unsigned short otherinfo1, 
	unsigned short otherinfo2, 
	unsigned short otherinfo3, 
	unsigned short otherinfo4, 
	unsigned short otherinfo5);
	
unsigned long hextoint(const char *string);


int main(int argc, char *argv[]) 
/* 
	To call this program:
	
	keymaker-pp "strSoftwareID", "strVer", intKeyType ,"strRegName", "strFingerPrint"
	
	"strSoftwareID" can be one of "DHTMLMenu", "Quicker", "Glanda", "Decompiler", "FlashVideoEnc","DVDRipper","TreeMenu"
	"strVer" can be 
		"MX2005" for Decompiler MX 2005, "MX2005A" for Decomiper MX 2005a
		"5.0" for DHTMLMenu corresponde to the version 5.0
		"1.6" for Quicker corresponde to version 1.6
		"2005" for Glanda corresponde to version 2005
		"1.0" for FlashVideoEnc corresponde to version 1.0
		"1.0" for DVDRipper corresponde to version 1.0
		"1.0" for TreeMenu corresponde to version 1.0
        "1.0" for iPodConverter corresponde to version 1.0
        "1.0" for SWFtoVideo corresponse to version 1.0
	intKeyType can be 0 for temprory key, or 1 for permanent key
	
	Example:
		keymaker-pp DHTMLMenu 4.6 1 lili@sothink.com.cn AADD-79B4
	

*/

{
	char username[512]="", privatekey[512]="";
	unsigned short otherinfo[5]={0,0,0,0,0};
	unsigned long hardwareID=0;
	int x, y, intKeyType, level=-1;
	
	if (argc != 6)
	{	
		// printf("%d\n", argc);
		return 1;
	}
		
	
	//for (x=0; x<argc; x++)
	//	printf("%s\n", argv[x]);
		
		
	intKeyType = atoi(argv[3]);
		
	/* reference to version and intKeyType	
	if (strcmp(argv[1], "DHTMLMenu") == 0)
	{
		if (strcmp(argv[2], "7.0") == 0)
		{
			strcpy(privatekey, "{36CDDD60-C5D6-4329-8F3C-654697EB3874}");
			hardwareID = 0;
		}
		else
		{
			if (0 == intKeyType)
			{
				strcpy(privatekey, "{BC2B1497-5387-43c5-A1A4-E955D55F0196}");
				hardwareID = 0;
			}
			else if (1 == intKeyType)
			{
				strcpy(privatekey, "{EE5BB963-D12C-430d-A351-C41F561FB60A}");
				hardwareID = hextoint(argv[5]);
			}
		}

	}
	*/
	if (strcmp(argv[1], "DHTMLMenu") == 0)
	{
			strcpy(privatekey, "{090A3FDC-4BF7-4d65-B8B7-BDF734507FA1}");
			hardwareID = 0;
	}
	else if (strcmp(argv[1], "Decompiler") == 0)
	{
			strcpy(privatekey, "{16AB1DDE-E62E-4847-8EDE-28C937ACEB2F}");
			hardwareID = 0;
	}
	else if (strcmp(argv[1], "Quicker") == 0)
	{
			strcpy(privatekey, "{1112800B-5DCB-4829-836E-6F0C23DA25D8}");
			hardwareID = 0;
	}
	else if (strcmp(argv[1], "Glanda") == 0)
	{
			strcpy(privatekey, "{4221B487-9D52-4fda-955F-D1C1AC9AAC65}");
			hardwareID = 0;
	}
	else if (strcmp(argv[1], "TreeMenu") == 0)
	{
		strcpy(privatekey, "{80B052FF-69D5-4396-A1E2-CE64B976A5D3}");
		hardwareID = 0;
	}
	else if (strcmp(argv[1], "FlashVideoEnc") == 0)
	{
		strcpy(privatekey, "{86856155-01AD-4d0a-89B1-A6FC207D8ED7}");
		hardwareID = 0;
	}
    else if (strcmp(argv[1], "SWFtoVideo") == 0)
	{
		strcpy(privatekey, "{30CDC856-B061-44c1-B4F7-4BA0A5747064}");
		hardwareID = 0;
	}
	else if (strcmp(argv[1], "WebVideoFF") == 0)
	{
		strcpy(privatekey, "{C883E45C-9A2E-488b-86E1-5C6F825D6B8B}");
		hardwareID = 0;
	}
	else if (strcmp(argv[1], "DHTMLMenuLite") == 0)
	{
		strcpy(privatekey, "{6B529579-1CA1-4f15-9617-F571CF537F58}");
		hardwareID = 0;
	}
	else if (strcmp(argv[1], "Scroller") == 0)
	{
		strcpy(privatekey, "{15DC848A-AA9A-48d7-B64F-CDA1EB7D95DC}");
		hardwareID = 0;
	}
	else
		return 1;

	if (!strlen(argv[4])) return 1;

	printf("%s\n", CreateShortV3Key(9, privatekey, argv[4], hardwareID, 0, 0, 0, 0, 0));

	return 0;
};
