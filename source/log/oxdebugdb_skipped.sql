select oxv_oxdiscount_#NUMVALUE#.oxid, oxv_oxdiscount_#NUMVALUE#.oxshopid, oxv_oxdiscount_#NUMVALUE#.oxshopincl, oxv_oxdiscount_#NUMVALUE#.oxshopexcl, oxv_oxdiscount_#NUMVALUE#.oxactive, oxv_oxdiscount_#NUMVALUE#.oxactivefrom, oxv_oxdiscount_#NUMVALUE#.oxactiveto, oxv_oxdiscount_#NUMVALUE#.oxtitle, oxv_oxdiscount_#NUMVALUE#.oxamount, oxv_oxdiscount_#NUMVALUE#.oxamountto, oxv_oxdiscount_#NUMVALUE#.oxpriceto, oxv_oxdiscount_#NUMVALUE#.oxprice, oxv_oxdiscount_#NUMVALUE#.oxaddsumtype, oxv_oxdiscount_#NUMVALUE#.oxaddsum, oxv_oxdiscount_#NUMVALUE#.oxitmartid, oxv_oxdiscount_#NUMVALUE#.oxitmamount, oxv_oxdiscount_#NUMVALUE#.oxitmmultiple from oxv_oxdiscount_#NUMVALUE# where (   oxv_oxdiscount_#NUMVALUE#.oxactive = #NUMVALUE#  or  ( oxv_oxdiscount_#NUMVALUE#.oxactivefrom < '#VALUE#' and oxv_oxdiscount_#NUMVALUE#.oxactiveto > '#VALUE#' ) )  and (
            select
                if(EXISTS(select #NUMVALUE# from oxobject#NUMVALUE#discount where oxobject#NUMVALUE#discount.OXDISCOUNTID=oxv_oxdiscount_#NUMVALUE#.OXID and oxobject#NUMVALUE#discount.oxtype='#VALUE#' LIMIT #NUMVALUE#),
                        #NUMVALUE#,
                        #NUMVALUE#) &&
                if(EXISTS(select #NUMVALUE# from oxobject#NUMVALUE#discount where oxobject#NUMVALUE#discount.OXDISCOUNTID=oxv_oxdiscount_#NUMVALUE#.OXID and oxobject#NUMVALUE#discount.oxtype='#VALUE#' LIMIT #NUMVALUE#),
                        #NUMVALUE#,
                        #NUMVALUE#) &&
                if(EXISTS(select #NUMVALUE# from oxobject#NUMVALUE#discount where oxobject#NUMVALUE#discount.OXDISCOUNTID=oxv_oxdiscount_#NUMVALUE#.OXID and oxobject#NUMVALUE#discount.oxtype='#VALUE#' LIMIT #NUMVALUE#),
                        #NUMVALUE#,
                        #NUMVALUE#)
            )
-- -- ENTRY END
select oxv_oxarticles_#NUMVALUE#.oxid, oxv_oxarticles_#NUMVALUE#.oxtitle, oxv_oxarticles_#NUMVALUE#.oxicon, oxv_oxarticles_#NUMVALUE#.oxparentid, oxv_oxarticles_#NUMVALUE#.oxvarcount, oxv_oxarticles_#NUMVALUE#.oxvarstock, oxv_oxarticles_#NUMVALUE#.oxstock, oxv_oxarticles_#NUMVALUE#.oxstockflag, oxv_oxarticles_#NUMVALUE#.oxprice, oxv_oxarticles_#NUMVALUE#.oxvat, oxv_oxarticles_#NUMVALUE#.oxunitquantity, oxv_oxarticles_#NUMVALUE#.oxshopid, oxv_oxarticles_#NUMVALUE#.oxthumb, oxv_oxarticles_#NUMVALUE#.oxactive, oxv_oxarticles_#NUMVALUE#.oxunitname, oxv_oxarticles_#NUMVALUE#.oxartnum, oxv_oxarticles_#NUMVALUE#.oxvarselect, oxv_oxarticles_#NUMVALUE#.oxvarname, oxv_oxarticles_#NUMVALUE#.oxpic#NUMVALUE#, oxv_oxarticles_#NUMVALUE#.oxshortdesc, oxv_oxarticles_#NUMVALUE#.oxtprice from oxv_oxarticles_#NUMVALUE# where  oxv_oxarticles_#NUMVALUE#.oxparentid ='#VALUE#'  order by oxv_oxarticles_#NUMVALUE#.oxsort
-- -- ENTRY END
select * from oxcontents where oxactive = '#VALUE#' and oxtype = '#VALUE#' and oxsnippet = '#VALUE#' and oxshopid = '#VALUE#' and oxcatid is not null order by oxloadid
-- -- ENTRY END
select * from oxcontents where oxactive = '#VALUE#' and oxtype = '#VALUE#' and oxsnippet = '#VALUE#' and oxshopid = '#VALUE#'  order by oxloadid
-- -- ENTRY END
select oxvarname, oxvartype, DECODE( oxvarvalue, '#VALUE#') as oxvarvalue from oxconfig where oxshopid = '#VALUE#'
-- -- ENTRY END
select oxv_oxselectlist_#NUMVALUE#.* from oxobject#NUMVALUE#selectlist left join oxv_oxselectlist_#NUMVALUE# on oxv_oxselectlist_#NUMVALUE#.oxid=oxobject#NUMVALUE#selectlist.oxselnid where oxobject#NUMVALUE#selectlist.oxobjectid='#VALUE#'  order by oxobject#NUMVALUE#selectlist.oxsort 
-- -- ENTRY END
select oxv_oxattribute_#NUMVALUE#.oxtitle, o#NUMVALUE#a.* from oxobject#NUMVALUE#attribute as o#NUMVALUE#a left join oxv_oxattribute_#NUMVALUE# on oxv_oxattribute_#NUMVALUE#.oxid = o#NUMVALUE#a.oxattrid where o#NUMVALUE#a.oxobjectid = '#VALUE#' and o#NUMVALUE#a.oxvalue != '#VALUE#' order by o#NUMVALUE#a.oxpos, oxv_oxattribute_#NUMVALUE#.oxpos 
-- -- ENTRY END
select oxv_oxdiscount_#NUMVALUE#.oxid, oxv_oxdiscount_#NUMVALUE#.oxshopid, oxv_oxdiscount_#NUMVALUE#.oxshopincl, oxv_oxdiscount_#NUMVALUE#.oxshopexcl, oxv_oxdiscount_#NUMVALUE#.oxactive, oxv_oxdiscount_#NUMVALUE#.oxactivefrom, oxv_oxdiscount_#NUMVALUE#.oxactiveto, oxv_oxdiscount_#NUMVALUE#.oxtitle_#NUMVALUE# as oxtitle, oxv_oxdiscount_#NUMVALUE#.oxamount, oxv_oxdiscount_#NUMVALUE#.oxamountto, oxv_oxdiscount_#NUMVALUE#.oxpriceto, oxv_oxdiscount_#NUMVALUE#.oxprice, oxv_oxdiscount_#NUMVALUE#.oxaddsumtype, oxv_oxdiscount_#NUMVALUE#.oxaddsum, oxv_oxdiscount_#NUMVALUE#.oxitmartid, oxv_oxdiscount_#NUMVALUE#.oxitmamount, oxv_oxdiscount_#NUMVALUE#.oxitmmultiple from oxv_oxdiscount_#NUMVALUE# where (   oxv_oxdiscount_#NUMVALUE#.oxactive = #NUMVALUE#  or  ( oxv_oxdiscount_#NUMVALUE#.oxactivefrom < '#VALUE#' and oxv_oxdiscount_#NUMVALUE#.oxactiveto > '#VALUE#' ) )  and (
            select
                if(EXISTS(select #NUMVALUE# from oxobject#NUMVALUE#discount where oxobject#NUMVALUE#discount.OXDISCOUNTID=oxv_oxdiscount_#NUMVALUE#.OXID and oxobject#NUMVALUE#discount.oxtype='#VALUE#' LIMIT #NUMVALUE#),
                        EXISTS(select oxobject#NUMVALUE#discount.oxid from oxobject#NUMVALUE#discount where oxobject#NUMVALUE#discount.OXDISCOUNTID=oxv_oxdiscount_#NUMVALUE#.OXID and oxobject#NUMVALUE#discount.oxtype='#VALUE#' and oxobject#NUMVALUE#discount.OXOBJECTID='#VALUE#'),
                        #NUMVALUE#) &&
                if(EXISTS(select #NUMVALUE# from oxobject#NUMVALUE#discount where oxobject#NUMVALUE#discount.OXDISCOUNTID=oxv_oxdiscount_#NUMVALUE#.OXID and oxobject#NUMVALUE#discount.oxtype='#VALUE#' LIMIT #NUMVALUE#),
                        EXISTS(select oxobject#NUMVALUE#discount.oxid from oxobject#NUMVALUE#discount where oxobject#NUMVALUE#discount.OXDISCOUNTID=oxv_oxdiscount_#NUMVALUE#.OXID and oxobject#NUMVALUE#discount.oxtype='#VALUE#' and oxobject#NUMVALUE#discount.OXOBJECTID='#VALUE#'),
                        #NUMVALUE#) &&
                if(EXISTS(select #NUMVALUE# from oxobject#NUMVALUE#discount where oxobject#NUMVALUE#discount.OXDISCOUNTID=oxv_oxdiscount_#NUMVALUE#.OXID and oxobject#NUMVALUE#discount.oxtype='#VALUE#' LIMIT #NUMVALUE#),
                        EXISTS(select oxobject#NUMVALUE#discount.oxid from oxobject#NUMVALUE#discount where oxobject#NUMVALUE#discount.OXDISCOUNTID=oxv_oxdiscount_#NUMVALUE#.OXID and oxobject#NUMVALUE#discount.oxtype='#VALUE#' and oxobject#NUMVALUE#discount.OXOBJECTID in ('#VALUE#') ),
                        #NUMVALUE#)
            ) 
-- -- ENTRY END
