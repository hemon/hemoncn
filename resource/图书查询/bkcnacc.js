/* BkcnAccess.js������������Ŀ��¼�ĸ����1998/09/10��by bifeng */

var record;
var jj = 0;

var number = "�٢ڢۢܢݢޢߢ���";

function PrintNumber(no)
{
  if(no < 10)
  {
    if(number.length > 10) // navigator.appName==Netscape
      document.write(number.substring(no * 2, no * 2 + 2));
    else                   // navigator.appName==Microsoft Internet Explorer
      document.write(number.substring(no, no + 1));
  }
  else
  {
    no++;
    document.write("(" + no + ")");
  }
}

function SimpleDollarA(fldname, from, repeat)
{
  var ii, idx = 0;
  var field, subfield;

  while(1)
  {
    field = record.GetFld(fldname, idx++);
    if(field == null) break;

    for(ii = 0; ; ii++)
    {
      subfield = field.GetSubFld("a", ii);
      if(subfield == null) break;

      PrintNumber(jj++);
      document.write("<a href=/dtcgibin/cgi32.exe?se=/ͼ���ܿ�/" + from);
      document.write("&Key=" + EncodeToNetString(subfield.content) + ">" + subfield + "</a>");
    }

    if(repeat == 0) break;
  }
}

function SubjectDollars(fldname)
{
  var ii, aa, idx = 0;
  var subject, sf;

  var field, group, subfield;
  var depart = 0;

  while(1)
  {
    field = record.GetFld(fldname, idx++);
    if(field == null) break;

    for(ii = 0, subject = ""; ; ii++)
    {
      group = field.GetGroup("a", ii);
      if(group == null)
      { 
        depart = 1;
        break;
      }

      for(aa = 0, subject = ""; ; aa++)
      {
        subfield = group.GetSubFld("", aa);
        if(subfield == null) break;

        sf = subfield.name;
        if(sf == "a")
          subject += subfield.content;
        else if((sf == "x") || (sf == "y") || (sf == "z"))
          subject += "--" + subfield.content;
      }

      if(subject != "")
      {
        PrintNumber(jj++);
        document.write("<a href=/dtcgibin/cgi32.exe?se=/ͼ���ܿ�/����");
        document.write("&Key=" + EncodeToNetString(subject) + ">" + subject + "</a>");
      }
    }

    if(depart == 1) break;
  }
}

record = new Record(document.record.path.value, document.record.marc.value);

document.write("<p>����.");
jj = 0;

SimpleDollarA("200", "����", 0);
SimpleDollarA("517", "����", 1);

document.write("����.");
jj = 0;

SimpleDollarA("700", "����", 0);
SimpleDollarA("701", "����", 1);
SimpleDollarA("702", "����", 1);

SimpleDollarA("710", "����", 0);
SimpleDollarA("711", "����", 1);
SimpleDollarA("712", "����", 1);

document.write("����.");
jj = 0;

SubjectDollars("600");
SubjectDollars("601");
SubjectDollars("605");
SubjectDollars("606");
SubjectDollars("607");
SimpleDollarA("610", "����", 1);

document.write("����.");
jj = 0;

SimpleDollarA("690", "�����", 1);
SimpleDollarA("692", "�����", 1);

/* bottom of BkcnAccess.js */
