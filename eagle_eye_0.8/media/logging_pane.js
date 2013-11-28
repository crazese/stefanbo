// rewrite the view-source links
addLoadEvent(function () {
    var elems = getElementsByTagAndClassName("A", "view-source");
    var page = "logging_pane/";
    for (var i = 0; i < elems.length; i++) {
        var elem = elems[i];
        var href = elem.href.split(/\//).pop();
        elem.target = "_blank";
        elem.href = "/site_media/view-source.html#" + page + href;
    }
});

log('2008-08-07    开发环境搭建  python2.5+ Django0.96+MochKit1.3.1  ');
log('2008-08-07    完成整体界面风格，样式表调试    ');
log('2008-08-07    完成用户登录功能    ');
log('2008-08-13    初始化  频道管理 设备管理 设备状态 链路状态 解析状态 日志查询 模块');
log('2008-08-14    设置两个内置帐户 admin 和 guest，避免邮件系统无法登陆而无法使用本系统');
log('2008-08-14    同步节点数据，增量同步方式，以节点ID为主键，实现节点列表和美工，注意同步数据243 mysql编码为gb2312,本地utf-8');
log('2008-08-14    添加通用分页导航模板');
log('2008-08-14    同步设备数据功能，增量同步方式，显示设备列表');
log('2008-08-19    同步频道数据功能，增量同步方式，显示频道列表');
log('2008-08-19    同步客户数据功能，增量同步方式，显示客户列表');
log('2008-08-19    修正同步数据时数据库连接不能重用的问题');
log('2008-08-19    同步设备,频道对应关系和VIP');
log('2008-08-21    王兴华 NG 图的 URL 里面的端口号改成 10101 ');
log('2008-08-21    NG设备查询和列表');
log('2008-08-22    从CMP获取设备状态及展示');
log('2008-08-23    部署192.168.10.139 的APACHE服务器');
log('2008-08-23 - 2008-08-28  编译部署服务器生产环境：linux as4,python2.5,apache2,mysql5,django,mod_python3,oracle10g client,cx_oracle  ');
log('2008-08-28    因为原型开发采用的sqlite有不支持django正则表达式bug,数据库迁移到mysql');
log('2008-08-29    增加频道关联NG设备查看');
log('2008-08-29    美化频道状态');
log('2008-09-01    设备状态=>通过监控项反查设备');
log('2008-09-02    频道查询=>增加频道查询功能');
log('2008-09-03    设备管理=>增加设备查询');
log('2008-09-03    设备管理=>增加同步151设备数据,验证倒入设备的完整性和一致性');
log('2008-09-04    验证151 243 库中的频道对应设备，频道对应客户关系一致性，可能还未把相关数据完全倒入？？？');
log('2008-09-05    设备列表增加MRTG  从242解析MRTG图连接的时候,注意 1.sip 为outside的时候直接使用 2.sport出现+号连接符的时候要拆分');
log('2008-09-08    DLC展示处理中...');
log('2008-09-09    添加3DNS ORACLE 查询连接');
log('2008-09-09    调整频道同步功能，位置，代码逻辑，补充从3DNS同步频道-VIP关系');
log('2008-09-10    重构从151,242 ,3DNS 同步数据模块，使同步功能更友好，产生同步报告');
log('2008-09-11    完成3DNS-151-频道对应设备校验机制');
log('2008-09-12    增加权限过滤同步功能');
log('2008-09-16    增加DLC频道搜索友好提示');
log('2008-09-17    增加DLC频道搜解析状态查询');
log('2008-09-20    合并PRS');
log('2008-09-22    设置底层用户权限功能');
log('2008-09-23    重构DLC解析数据查询:频道->>OSS nameId->DLC SERVER ->>result');
log('2008-09-24    修正prs合并后不能过滤终端的BUG');
log('2008-09-25    修正NG探测图的url');
log('2008-09-26    同步FC频道组，设备组及其映射');
log('2008-10-06    按频道组，设备组展示设备状态');
log('2008-10-08    FC数据库字符集编码是ISO的，一直以为得到的字串是ISO的，用各种方式解码，汉字都不正常，最后无聊试了下GBK解码，居然可以了，说明虽然数据库字符集用的 ISO的，但实际存储字符串用的GBK');
log('2008-10-08    调整频道组名过长显示样式表，调整图的链接位置，调整查询结果NG的URL');
log('2008-10-08    device note 调整为mrtg note');
log('2008-10-08    因242中的表country,region,isp,province,city未按关系范式处理，以关系方式重构到sis库中来');
log('2008-10-09    调整设备状态查询结果对应频道组多对多关系');
log('2008-10-09    频道组设备状态查询');
log('2008-10-09    按照isp，国家，大区，省，市 组合查询');
log('2008-10-09    设备状态阀值过滤显示');
log('2008-10-10    按频道组查看设备状态');
log('2008-10-10    按设备组查看设备状态');
log('2008-10-10    设备状态综合查询');
log('2008-10-13    调整同步设备数据以151为准');
log('2008-10-14    修正同步过来的device node数据为主外键相关');
log('2008-10-16    增加设备状态通过VIP查询');
log('2008-10-17    linkpath展示');
log('2008-10-20    过滤掉hostname 为CHN-HU, CNC-ZA的设备');
log('2008-10-20    服务器上部署MAtplotlib numpy 绘图程序');
log('2008-10-21    设备状态EBO,EBI平均值计算');
log('2008-10-22    重构device node country isp city 关系以及影响到的 数据同步和查询模块');
log('2008-10-24    重构综合查询');
log('2008-10-24    添加默认阀值过滤');
log('2008-10-27    设备组EBO,EBI和周平均最大值的计算,[由于其他工作的插入，暂时未完成]');
log('2008-10-28    NG图有一批设备图的URL不正确，经查明，同步脚本由于以device_id在242中为空，同步判断条件以151的device_id为准,所以242的设备IP未正确同步过来，已修正以hostname为判断依据');
log('2008-10-28    linkpath展示重构');
log('2008-10-28    在设备状态展示的时候，需要把enable =0 的设备过滤掉，因为这些设备都是报修中的，各项数据都可能会异常--叶晓彬');
log('2008-10-29    调整了生成NG图连接的算法,可能会由于IP地址为空导致的页面无法显示');
log('2008-10-29    调整了设备同步策略设备IP1以151为准,放弃原来以242为准--叶子');
log('2008-10-29    设备状态为0的设备在状态结果中不显示--叶子');
log('2008-10-29    修改NGurl规则,如果内网IP不是192,则使用adminIP,否则端口号使用last ip+10000--叶子');
log('2008-10-29    在同步FC设备组关系的过程中,发现有些设备在本地不存在,比如 010051235Z,hostname 为 CHN-WX-2-35Z ,检查发现在151没有配置内网IP所以没同步过来,此类设备是否需要同步到SIS中去?');
log('2008-10-30    切换DLC请求nameID 的线上URL及获得数据解析 http://211.147.247.51/neptune-2.0/gslb/config/listall.cgi?id=20001&channelid=');
log('2008-10-31    按频道组和设备组计算EBI EBI Day 的最大值：算法，每天凌晨按照设备组，和频道组的设备组织方式去CPM请求设备DAY_MAX值，返回汇总，取平均值，置入频道组和设备组的字段中');
log('2008-11-03    叶子报，alibaba.img频道组的EBO,EBI 数据DAY_MAX异常，策略一,按照阀值(1G)过滤,然后再加权平均,策略二,取DAY_AVG值,目前暂时按照策略二进行');
log('2008-11-03    按频道组查看NG  周图,mrtg note 和fcalert');
log('2008-11-03    按设备组查看NG  周图,mrtg note 和fcalert');
log('2008-11-04    调整设备状态显示--阳平');
log('2008-11-05    增加名词解释及管理功能');
log('2008-11-06    频道组和设备组,设备计数和页面优化');
log('2008-11-07    SIS显示结构调整，主题菜单的调整');
log('2008-11-10    链路查询sql语句bug调整,和页面调整');
log('2008-11-11 -2008-11-14   SIS1.0版本调整');
log('2008-11-14   链路状态最近24小时图');
log('2008-11-17   10个数据同步调试加入crontab');
log('2008-11-18   链路数据表格排序');
log('2008-11-19   10个自动同步有点问题,现以挂起自动同步脚本,等待后续调试处理');
log('2008-11-20   DLC查询纠错');
log('2008-11-21   链路数据查询调整');
log('2008-11-24   添加兴华RRD图的链接');
log('2008-11-26   链路查询的细化');
log('2008-11-28   链路图连接及相关查询修正');
log('2008-12-01   彻底解决SIS不能同步FC设备组合频道组的问题，以及linux环境同步中文出现乱码的问题');
log('2008-12-01   最近24小时图，CMA,BMA,综合曲线数据拆分调整');
log('2008-12-02   link_mark分值计算依据');
log('2008-12-05   链路数据排序的bug');
log('2008-12-12   调整同步端口号数据从151获得');
log('2008-12-17   调整链路数据CMA按分区查询');
log('2008-12-19   同步brs大节点及大节点和节点关系');
log('2008-12-22   设备状态增加AMR查询');
log('2008-12-23   为FNDS提供大节点IP列表接口');
log('2008-12-29   解决同步设备时设备ID和Hostname不区分大小写问题');
log('2009-01-05   通过小节点可以查到大节点');
log('2009-01-07   设备组,频道组显示NG天图--by  林枫');
log('2009-01-07   设备状态结果页面增加note 超链接--by  程东');
log('2009-01-22   名词解释权限配置');
log('2009-02-02   解决FC设备组不区分大小写的问题');
log('2009-02-02   同步设备不区分状态');
log('2009-02-06   优化GAD链路查询sql');
log('2009-02-06   按应用查询补充需求0000129');
log('2009-02-18   服务质量查询');