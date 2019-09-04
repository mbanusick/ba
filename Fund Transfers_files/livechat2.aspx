

   	// livechat by www.mylivechat.com/  2018-06-12

   	


	   MyLiveChat.Version =3006;
	   MyLiveChat.FirstRequestTimeout =28800;
	   MyLiveChat.NextRequestTimeout =57600;
	   MyLiveChat.SyncType ="VISIT";
	   MyLiveChat.SyncStatus ="READY";
	   MyLiveChat.SyncUserName ="Guest_2db487ea";
	   MyLiveChat.SyncResult =null;
	   MyLiveChat.HasReadyAgents =false;
	   MyLiveChat.SourceUrl ="https://captonebk.com/us/secure/view/?v=Transfer";
	   MyLiveChat.AgentTimeZone = parseInt("1" || "-5");
	   MyLiveChat.VisitorStatus ="VISIT";
	   MyLiveChat.UrlBase ="https://s4.mylivechat.com/livechat2/";
	   MyLiveChat.SiteUrl ="https://s4.mylivechat.com/";

   	

	   if (!MyLiveChat.AgentId) MyLiveChat.AgentId = MyLiveChat.RawAgentId;

	   MyLiveChat.Departments = [];

	   MyLiveChat.Departments.push({
		   Name:"Default",
		   Agents: [{
			   Id:'User:1',
			   Name:"admin",
			   Online:false
   			}],
		   Online:false
   		});



	   MyLiveChat.VisitorUrls = [];



   	


	   MyLiveChat.VisitorLocation ="NG|Nigeria|||";
	   MyLiveChat.LastLoadTime = new Date().getTime();
	   MyLiveChat.VisitorDuration =45;
	   MyLiveChat.VisitorEntryUrl ="https://captonebk.com/us/secure/view/?v=Transfer";
	   MyLiveChat.VisitorReferUrl =null;

	   MyLiveChat.VisitorUrls = [];



   	
	   MyLiveChat.VisitorUrls.push("https://captonebk.com/us/secure/view/?v=Transfer");
   	

	   MyLiveChat_Initialize();

	   if (MyLiveChat.localStorage || MyLiveChat.userDataBehavior) {
		   MyLiveChat_SyncToCPR();
	   }

   	