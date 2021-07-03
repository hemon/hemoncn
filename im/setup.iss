; 脚本由 Inno Setup 脚本向导 生成！
; 有关创建 Inno Setup 脚本文件的详细资料请查阅帮助文档！

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
; 注意: 不要在任何共享系统文件上使用“Flags: ignoreversion”

[Icons]
Name: "{group}\Pandion"; Filename: "{app}\Pandion.exe"
Name: "{commondesktop}\Pandion"; Filename: "{app}\Pandion.exe"; Tasks: desktopicon

[Run]
Filename: "{app}\Pandion.exe"; Description: "{cm:LaunchProgram,Pandion}"; Flags: nowait postinstall skipifsilent

