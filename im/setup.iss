; �ű��� Inno Setup �ű��� ���ɣ�
; �йش��� Inno Setup �ű��ļ�����ϸ��������İ����ĵ���

[Setup]
AppName=Pandion
AppVerName=Pandion 2.5
AppPublisher=Pandion
AppPublisherURL=http://www.Pandion.be
AppSupportURL=http://www.Pandion.be
AppUpdatesURL=http://www.Pandion.be
DefaultDirName={pf}\Pandion
DefaultGroupName=Pandion
OutputBaseFilename=setup
SetupIconFile=D:\Program Files\Pandion\images\brand\installer.ico
Compression=lzma
SolidCompression=yes

[Languages]
Name: "chinese"; MessagesFile: "compiler:Default.isl"

[Tasks]
Name: "desktopicon"; Description: "{cm:CreateDesktopIcon}"; GroupDescription: "{cm:AdditionalIcons}"; Flags: unchecked

[Files]
Source: "D:\Program Files\Pandion\Pandion.exe"; DestDir: "{app}"; Flags: ignoreversion
Source: "D:\Program Files\Pandion\*"; DestDir: "{app}"; Flags: ignoreversion recursesubdirs createallsubdirs
; ע��: ��Ҫ���κι���ϵͳ�ļ���ʹ�á�Flags: ignoreversion��

[Icons]
Name: "{group}\Pandion"; Filename: "{app}\Pandion.exe"
Name: "{commondesktop}\Pandion"; Filename: "{app}\Pandion.exe"; Tasks: desktopicon

[Run]
Filename: "{app}\Pandion.exe"; Description: "{cm:LaunchProgram,Pandion}"; Flags: nowait postinstall skipifsilent

