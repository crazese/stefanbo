from django.conf.urls.defaults import patterns, include, url

# Uncomment the next two lines to enable the admin:


urlpatterns = patterns('',
    # Examples:
    # url(r'^$', 'eagle_eye.views.home', name='home'),
    # url(r'^eagle_eye/', include('eagle_eye.foo.urls')),

    # Uncomment the admin/doc line below to enable admin documentation:
    # url(r'^admin/doc/', include('django.contrib.admindocs.urls')),

    # Uncomment the next line to enable the admin:
    (r'^success/$','net_bw.views.success'),
    (r'^showRoom/$','net_bw.views.room_list'),
    (r'^addRoom/$','net_bw.views.ini_list'),
    (r'^searchRoom/$','net_bw.views.room_search'),
    (r'^r_add/$','net_bw.views.room_add'),
    (r'^ip_room/$','net_bw.views.ip_room'),
    (r'^ip_search/$','net_bw.views.ip_search'),
    (r'^contactIP/$','net_bw.views.contactIP'),
    (r'^contact/(\d{1,4})/$','net_bw.views.contact'),
    (r'^contactAdd/(\d{1,4})/$','net_bw.views.add_contact'),
    (r'^contactIP_add/(\d{1,4})/$','net_bw.views.contactIP_add'),
    (r'^modifyRoom/(\d{1,4})/$','net_bw.views.room_modify'),
    (r'^modifyHandle/(\d{1,4})/$','net_bw.views.modify_handle'),
    (r'^show_bw_modify/$','net_bw.views.show_bw_modify'),
    (r'^deleteRoom/(\d{1,4})/$','net_bw.views.room_delete'),
    (r'^manageRoom/(\d{1,4})/$','net_bw.views.room_manage'),
    (r'^room_manage/(\d{1,4})/$','net_bw.views.manage_room'),

    (r'^room_bw/(\d{1,2})/$','net_bw.bw_views.index'),
    (r'^submit/(\d{1,2})/$','net_bw.bw_views.submit'),
    (r'^add/(\d{1,2})/$','net_bw.bw_views.add'),
    (r'^modify/(\d{1,20})/$','net_bw.bw_views.modify'),
    (r'^delete/(\d{1,20})/$','net_bw.bw_views.delete'),

    (r'^day_report/(\d{1,2})/$','net_bw.bw_views.day_report'),
    (r'^day_isp/(\d{1,2})/$','net_bw.bw_views.day_isp'),
    (r'^month_report/(\d{1,2})/(.*)/$','net_bw.bw_views.month_report'),
    (r'^month_isp/(\d{1,2})/$','net_bw.bw_views.month_isp'),
    (r'^week_report/(\d{1,2})/$','net_bw.bw_views.week_report'),
    (r'^week_isp/(\d{1,2})/$','net_bw.bw_views.week_isp'),

    (r'^day_area/(\d{1,2})/$','net_bw.bw_views.day_area'),
    (r'^week_area/(\d{1,2})/$','net_bw.bw_views.week_area'),
    (r'^month_area/(\d{1,2})/$','net_bw.bw_views.month_area'),

    (r'^week_chart/(\d{1,20})/(.*)/$','net_bw.bw_views.week_chart'),
    (r'^month_chart/(\d{1,30})/(.*)/$','net_bw.bw_views.month_chart'),

    (r'^isp_week_chart/(\d{1,20})/(.*)/$','net_bw.bw_views.isp_week_chart'),
    (r'^isp_month_chart/(\d{1,20})/(.*)/$','net_bw.bw_views.isp_month_chart'),
    (r'^area_week_chart/(\d{1,20})/(\d{1,4})/(.*)/$','net_bw.bw_views.area_week_chart'),
    (r'^area_month_chart/(\d{1,20})/(\d{1,4})/(.*)/$','net_bw.bw_views.area_month_chart'),

    (r'^bw_report/(\d{1,2})/$','net_bw.bw_views.bw_report'),
    (r'^bw_chart/(\d{1,20})/(.*)/$','net_bw.bw_views.bw_chart'),
)
