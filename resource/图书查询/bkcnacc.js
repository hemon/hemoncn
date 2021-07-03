/* BkcnAccess.js，用于生成书目记录的根查项，1998/09/10，by bifeng */

var record;
var jj = 0;

var number = "①②③④⑤⑥⑦⑧⑨⑩";

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
      document.write("<a href=/dtcgibin/cgi32.exe?se=/图书总库/" + from);
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
        document.write("<a href=/dtcgibin/cgi32.exe?se=/图书总库/主题");
        document.write("&Key=" + EncodeToNetString(subject) + ">" + subject + "</a>");
      }
    }

    if(depart == 1) break;
  }
}

record = new Record(document.record.path.value, document.record.marc.value);

document.write("<p>　Ⅰ.");
jj = 0;

SimpleDollarA("200", "书名", 0);
SimpleDollarA("517", "书名", 1);

document.write("　Ⅱ.");
jj = 0;

SimpleDollarA("700", "作者", 0);
SimpleDollarA("701", "作者", 1);
SimpleDollarA("702", "作者", 1);

SimpleDollarA("710", "作者", 0);
SimpleDollarA("711", "作者", 1);
SimpleDollarA("712", "作者", 1);

document.write("　Ⅲ.");
jj = 0;

SubjectDollars("600");
SubjectDollars("601");
SubjectDollars("605");
SubjectDollars("606");
SubjectDollars("607");
SimpleDollarA("610", "主题", 1);

document.write("　Ⅳ.");
jj = 0;

SimpleDollarA("690", "分类号", 1);
SimpleDollarA("692", "分类号", 1);

/* bottom of BkcnAccess.js */
