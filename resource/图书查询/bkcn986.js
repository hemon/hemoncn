/* bkcn986.js�����ڴ�����Ŀ��¼986��ͨ�ֶΣ�1998/09/10��by bifeng */

var record, field, group, subfield;
var jybz, kjsl;
var ii, jj;

var xue,mm;
var ling,kk;
var group1,sbufield1;
var group2,sbufield2;
var long, li,an;
xue=EndofField();


var firstbook = "*";
var borrowed = 0;

record = new Record(document.record.path.value, document.record.marc.value);

field = record.GetFld("986", 0);
kjsl = 0;

 





if(field != null)
{
  for(ii = 0; ; ii++)
  {
    group = field.GetGroup("a", ii);
    if(group == null) break;


    subfield = group.GetSubFld("a", 0);
    if(subfield=="") 
    {
     
     break;
    }

    if(ii == 0)
    {
        document.write("<br>");
        document.write("<form method=get action=/dtcgibin/cgi32.exe>");

        document.write("<table border=1 width=490 cellspacing=0>");
        document.write("<td width=75 bgcolor=#DDDDDD>ԤԼ��־</td>");
        document.write("<td width=75 bgcolor=#FFCC64>ͼ������</td>");
        document.write("<td width=68 bgcolor=#80E0A0>������</td>");
        document.write("<td width=68 bgcolor=#80D0D0>Ӧ����</td>");
        document.write("<td width=68 bgcolor=#FFEE80>��ص�</td>");
        document.write("<td width=68 bgcolor=#80E8E8>��¼��</td>");
        document.write("<td width=68 bgcolor=#F0A0AA>����Ϣ</td>");
    }

    jybz = 1;
    document.write("<tr>");

    subfield = group.GetSubFld("a", 0);
    if(subfield == null)
        document.write("<td>&nbsp;</td>");
    else
    {
        if(firstbook == "*") firstbook += subfield.content;
        document.write("<td align=center>��");
        document.write("<input type=radio name=res value=" + subfield + "></td>");
        document.write("<td>" + subfield + "</td>");

        jing=subfield;
    
    }

    subfield = group.GetSubFld("r", 0);
    if(subfield == null)
    {
       document.write("<td>&nbsp;</td>");
    }
    else
    {
      document.write("<td><a href=/dtcgibin/cgi32.exe?se=/���߿�/֤�����");
      document.write("&Key=" + EncodeToNetString(subfield.content) + ">");
      document.write(subfield + "</a></td>");

      jybz = 0;
      borrowed++;
    }

    subfield = group.GetSubFld("t", 0);
    if(subfield == null)
       document.write("<td>&nbsp;</td>");
    else
       document.write("<td>" + subfield + "</td>");

    
    subfield = group.GetSubFld("b", 0);

    bb=ii;
    group1 = field.GetGroup("a", ii);
        for(mm=0; ;mm++)
    {
      group2 = field.GetGroup("a", mm);
    if(group2 == null) break;
    
    }
 kk=mm/2;
 

/*for(nn=kk;nn<=2*kk-1;nn++)
{
  xx=nn  
  l1=field.GetGroup("a", nn);
  x1=l1.GetSubFld("h", 0);
  j1=l1.GetSubFld("b", 0);
  
  if(group1 == group)
   break;
  else 
   break;  
}*/
  l1=field.GetGroup("a", ii+kk);
  x1=l1.GetSubFld("h", 0);
  j1=l1.GetSubFld("b", 0);
  
  
    if(j1== "a")document.write("<td>&nbsp;��ʦ</td>");
    
    if(j1== "b")document.write("<td>&nbsp;���</td>");
    if(j1== "o")document.write("<td>&nbsp;����</td>");
    if(j1== "g")document.write("<td>&nbsp;������</td>");
    if(j1== "e")document.write("<td>&nbsp;�ο�</td>");
    if(j1== null)document.write("<td>&nbsp;</td>");


 /*   if(subfield == null)
       document.write("<td>&nbsp;"+j1+"</td>");
    else
       document.write("<td>" + subfield + "</td>");
*/    
    



    subfield = group.GetSubFld("h", 0);
    if(subfield == null)
       document.write("<td>&nbsp;xue</td>");
    else
       document.write("<td>" + subfield + "</td>");


  



    subfield = group.GetSubFld("s", 0);
    if(subfield != null)
    {
      if(subfield.content.length > 0) jybz = 0;
    }

    if(jybz == 1)
    {
       
       if(j1=="a"||j1=="e"||j1=="g")       
       {document.write("<td >���ɽ�</td>");}
       else
       {kjsl++;
       document.write("<td bgcolor=#66ccff>�ɽ�</td>");       }
    }
    else
    {
       document.write("<td >���ɽ�</td>");
    }
  }

  

}

if(ii > 0)
  {
    document.write("<tr> <td colspan=7 align=center>");
    if(kjsl == 0)
      document.write("������ȫ������");
    else
      document.write("������л���" + kjsl + "�����Խ���");

    if(borrowed > 0)
    {
      document.write("<tr><td align=center>��");
      document.write("<input type=radio name=res value=" + firstbook + " checked></td>");

      document.write("<td colspan=6 align=center>");
      document.write("ѡ��ԤԼ�ֱ�־��ԤԼ��������ⵥ��</td>");
    }

    document.write("</table>");
    if(borrowed > 0)
    {
      document.write("<br>");
      document.write("��������<input type=submit value=ԤԼ�ύ>");
    }
    document.write("</form>");
  }


