const Scenario = require('../tutorial/scenario.js');

class ScenarioGoogleDown extends Scenario {

    constructor(tutorialComponent, chatDetails = null) {
        super(tutorialComponent, {
            noob: {
                name: "Olaf",
                chatTitle: "Chat (Joe the IT guru)",
            },
            guru: {
                name: "Joe",
                chatTitle: "Chat (Olaf)"
            }
        });
    }

    loadSteps() {
        let details = this.chatDetails;
        let noobBrowserWindow = this.tutorialComponent.display.noob.browserWindow;
        let terminalWindow = this.tutorialComponent.display.noob.terminalWindow;
        let noobChatWindow = this.tutorialComponent.display.noob.chatWindow;
        let guruChatWindow = this.tutorialComponent.display.guru.chatWindow;
        let guruBrowserWindow = this.tutorialComponent.display.guru.browserWindow;


        let prompt = (delay = 100) => {
            this.addStep(delay, () => {
                return terminalWindow.pushToTerm({ strings: [ '<br><span class="text-success">root</span>@main-serv /home/admin# ' ]})
            });
        };

        this.addStep(500, () => {
            terminalWindow.show = true;
            terminalWindow.active = true;
            return Promise.resolve();
        })

        this.addStep(500, () => {
            return terminalWindow.pushToTerm({ strings: [ 'login as: ' ]})
        })

        this.addStep(200, () => {
            return terminalWindow.pushToTerm({ strings: ['admin'], typeSpeed: 50, startDelay: 1000});
        })

        this.addStep(200, () => {
            return terminalWindow.pushToTerm({ strings: [ '<br>admin@main-serv\'s password: ' ]});
        })

        this.addStep(200, () => {
            return terminalWindow.pushToTerm({ strings: ['**********'], typeSpeed: 50, startDelay: 1000});
        })

        this.addStep(2000, () => {
            return Promise.resolve();
        })

        let lines = [
            'Welcome to Ubuntu Bionic Beaver',
            ' * Documentation:  https://help.ubuntu.com',
            ' * Management:     https://landscape.canonical.com',
            ' * Support:        https://ubuntu.com/advantage',
            'This system has been minimized by removing packages and content that are',
            'not required on a system that users do not log into.',
            'To restore this content, you can run the \'unminimize\' command.',
            'Last login: Thu '+(new Date()).toLocaleDateString()+' from 169.254.1.25'];

        lines.map((line) => {
            this.addStep(10, () => {
                return terminalWindow.pushToTerm({ strings: [ "<br>" + line ]});
            })
        })

        this.addStep(500, () => {
            return terminalWindow.pushToTerm({ strings: [ '<br><span class="text-success">admin</span>@main-serv ~$ ' ]})
        })

        this.addStep(1500, () => {
            return terminalWindow.pushToTerm({ dealy: 100, strings: ['sudo su -'], typeSpeed: 50});
        })

        prompt();

        this.addStep(1000, () => {
            return terminalWindow.pushToTerm({ dealy: 100, strings: ['curl localhost'], typeSpeed: 50});
        })


        this.addStep(10, () => {
            return terminalWindow.pushToTerm({ strings: ['<br><span class="text-danger">curl: (7) Failed to connect to localhost port 80: Connection refused<span>']});
        })

        prompt();

        this.addStep(1000, () => {
            return terminalWindow.pushToTerm({ dealy: 100, strings: ['ps aux | grep apache'], typeSpeed: 50});
        })

        this.addStep(10, () => {
            return terminalWindow.pushToTerm({ strings: [ '<br>root    12547  0.0  0.0  11460   972 pts/9    S+   00:12   0:00 grep --color=auto <span class="text-danger">apache</span>' ]})
        })

        prompt(100);

        this.addStep(1500, () => {
            return terminalWindow.pushToTerm({ dealy: 500, strings: ['systemctl start apache2.service'], typeSpeed: 60});
        })

        this.addStep(10, () => {
            return terminalWindow.pushToTerm({ strings: [ '<br><span class="text-danger">Failed to connect to bus: No such file or directory</span>' ]})
        })

        prompt();
        this.addStep(500, () => {
            return terminalWindow.pushToTerm({ dealy: 500, strings: ['service apache2 start'], typeSpeed: 60});
        })

        this.addStep(10, () => {
            return terminalWindow.pushToTerm({ strings: [ `<br><span class="text-danger"> * Starting Apache httpd web server apache2<br>
(13)Permission denied: AH00072: make_sock: could not bind to address 0.0.0.0:80<br>
no listening sockets available, shutting down<br>
AH00015: Unable to open logs<br>
Action 'start' failed.<br>
The Apache error log may have more information.
</span>` ]})
        })
 
        prompt();

        // chat communication
        this.addStep(2500, () => {
            noobChatWindow.title = details.noob.chatTitle;
            guruChatWindow.title = details.guru.chatTitle;

            terminalWindow.active = false;
            noobChatWindow.show = true;
            noobChatWindow.active = true;
            return Promise.resolve();
        })

        this.addStep(1000, () => {
            return noobChatWindow.sendChatMessage(details.noob.name, `Hi ${details.guru.name}, we're having problems with our website. HTTP server is not starting and I'm out of ideas why.`, guruChatWindow);
        })

        this.addStep(500, () => {
            guruChatWindow.show = true;
            guruChatWindow.active = true;

            return Promise.resolve();
        })

        this.addStep(1500, () => {
            return guruChatWindow.sendChatMessage(details.guru.name, "Did you check the logs?", noobChatWindow);
        })

        this.addStep(5000, () => {
            return noobChatWindow.sendChatMessage(details.noob.name, "Of course I did!", guruChatWindow);
        })

        this.addStep(2500, () => {
            return guruChatWindow.sendChatMessage(details.guru.name, "Can you share your console?", noobChatWindow);
        });

        this.addStep(1000, () => {
            return Promise.resolve();
        });

        this.genericSteps();

        let commands = [
            { type: '&nbsp;whoami', output: '<br>root<br>&gt;_ $ ', wait: 2000 },
            { type: 'service apache2 start', output: `<br><span class="text-danger"> * Starting Apache httpd web server apache2<br>
(13)Permission denied: AH00072: make_sock: could not bind to address 0.0.0.0:80<br>
no listening sockets available, shutting down<br>
AH00015: Unable to open logs<br>
Action 'start' failed.<br>
The Apache error log may have more information.
</span><br>&gt;_ $ `, wait: 2000 },
            { type: 'rm -r /var/run/apache2', output: '<br>&gt;_ $ ', wait: 1000 },
            { type: 'service apache2 start', output: `<br>* Starting Apache httpd web server apache2<br>&gt;_ $ `, wait: 500 },
            { type: 'curl -X HEAD -I http://localhost', output: `<br>HTTP/2 200
<br>expires: -1
<br>cache-control: private, max-age=0
<br>content-type: text/html; charset=ISO-8859-1
<br>p3p: CP="This is not a P3P policy! See g.co/p3phelp for more info."
<br>server: gws
<br>x-xss-protection: 0
<br>x-frame-options: SAMEORIGIN
<br>set-cookie: 1P_JAR=2019-10-21-20; expires=Wed, 20-Nov-2019 20:28:14 GMT; path=/; domain=.google.com; SameSite=none
<br>set-cookie: NID=189=Cjyo9RpUPAAshEPlZekSc-9owPJ85BuDDNQgEvdfwcmKox2YW3qvtV-Fnn-85NQ; expires=Tue, 21-Apr-2020 20:28:14 GMT; path=/; domain=.google.com; HttpOnly
<br>alt-svc: quic=":443"; ma=2592000; v="46,43",h3-Q048=":443"; ma=2592000,h3-Q046=":443"; ma=2592000,h3-Q043=":443"; ma=2592000
<br>accept-ranges: none
<br>vary: Accept-Encoding
<br>&gt;_ $ `, wait: 500 },
        ];

        commands.map( command => {
            this.addStep(command.wait, () => {
                return Promise.all([
                    noobBrowserWindow.pushToTerm({ strings: [ command.type ], typeSpeed: 50 }),
                    guruBrowserWindow.pushToTerm({ strings: [ command.type ], typeSpeed: 50 })
                ]);
            })

            this.addStep(100, () => {
                return Promise.all([
                    noobBrowserWindow.pushToTerm({ strings: [ command.output ], typeSpeed: 0 }),
                    guruBrowserWindow.pushToTerm({ strings: [ command.output ], typeSpeed: 0 })
                ]);
            })
        });

        this.addStep(1000, () => {
            guruChatWindow.active = true;
            guruBrowserWindow.active = false;
            return Promise.resolve();
        })

        this.addStep(500, () => {
            return guruChatWindow.sendChatMessage(details.guru.name, "Site is back up now", noobChatWindow);
        })

        this.addStep(1000, () => {
            noobChatWindow.active = true;
            noobBrowserWindow.active = false;

            return Promise.resolve();
        })

        this.addStep(1000, () => {
            return noobChatWindow.sendChatMessage(details.noob.name, "Thank you.", guruChatWindow);
        })

        setTimeout(() => {
            this.event.trigger("steps:loaded", this.steps.length);
        }, 100);
    }
}

module.exports = ScenarioGoogleDown;
