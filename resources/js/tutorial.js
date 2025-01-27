const xTerm = require('xterm');

const Terminal = xTerm.Terminal
import Typed from 'typed.js';

class Tutorial {

    constructor() {
        this.lineCounter = 0;
        this.queueDelay = 0;

        this.commandText = "curl --request POST --include --header \\\n'Content-Type: application/json'\\\n--data-binary @- --no-buffer \\\nhttp://lukas.localdev/LukaszTlalka/terminal-helper/check/test.php";
    }

    initDom(tutorWindow, chat1Window, chat2Window, browser1Window, browser2Window) {
        this.$tutorWindow = tutorWindow;
        this.$chatWindows = [ chat1Window, chat2Window ];
        this.$browserWindows = [ browser1Window, browser2Window ];

        this.$tutorTerminal = tutorWindow.find('.terminal');
        this.$chatMessages = [ chat1Window.find('.messages'), chat2Window.find('.messages') ];
    }

    scrollTerminal() {
        this.$tutorTerminal.animate({ scrollTop: this.$tutorTerminal.prop("scrollHeight")}, 1000);
    }

    pushToTerm(options = { typeSpeed: 0 }) {
        let lineID = "line-" + this.$tutorWindow.attr('id') + ++this.lineCounter;
        this.$tutorTerminal.append( "<span id='"+lineID+"'></span>" );

        $("#div1").animate({ scrollTop: $('#div1').prop("scrollHeight")}, 1000);

        var self = this;

        if (options.typeSpeed) {
            let typed = new Typed("#" + lineID, Object.assign({
                onComplete: function() {
                    setTimeout( () => {
                        $("#"+lineID).siblings('.typed-cursor').remove();
                    }, 200);
                    self.scrollTerminal();

                    resolve();
                }
            }, options));
        } else {
            options.strings.map( (line) => {
                $("#" + lineID).html(line);
            })
            this.scrollTerminal();
        }
    }

    queue(time, closure) {
        this.queueDelay += time;
        setTimeout(closure, this.queueDelay);
    }

    openConsoleWindow() {
        this.$tutorWindow.css({
            display: 'inline-block'
        }).attr('class', 'window zoomIn animated');
    }

    hideWindow(type, id) {
        let window = type == 'chat' ? this.$chatWindows[id] : this.$browserWindows[id];

        window.css({
            display: 'none'
        });
    }

    openWindow(type, id, title) {

        let window = type == 'chat' ? this.$chatWindows[id] : this.$browserWindows[id];

        window.find('.title-text').html(title);

        window.css({
            display: 'inline-block'
        }).attr('class', 'window zoomIn animated');
    }

    setBrowserUrl(browserId, url, delay = 50) {
        let typeMessageId = "#" + this.$browserWindows[browserId].attr('id') + " .type-message"
        $(typeMessageId).html("");

        if (delay == 0)
           return $(typeMessageId).html(url);

        let typed = new Typed(typeMessageId, {
            strings: [url],
            typeSpeed: delay,
            onComplete: () => {
                typed.destroy();
                $(typeMessageId).html(url);
            }
        });
    }

    sendChatMessage(chatId, from, msg, isReceived = false) {
        let lineID = "line-" + this.$tutorWindow.attr('id') + ++this.lineCounter;

        this.$chatMessages[chatId].append( "<div class='message' id='"+lineID+"'></div>" );

        let typeMessageId = "#" + this.$chatWindows[chatId].attr('id') + " .type-message"

        if (isReceived) {
            $("#" + lineID).html("<strong>" + from + ":</strong> " + msg);
            return;
        }

        let typed = new Typed(typeMessageId, {
            strings: [msg],
            typeSpeed: 20,
            onComplete: () => {
                setTimeout( () => {
                    typed.destroy();
                    $("#" + lineID).html("<strong>Me:</strong> " + msg);

                    this.sendChatMessage(chatId == 0?1:0, from, msg, true);
                }, 1000);
            }
        });
    }

    setupConnection() {
        this.queue(500, () => {
            this.openWindow('browser', 0, 'Web Browser');
        })

        this.queue(500, () => {
            this.setBrowserUrl(0, "https://www.terminalmirror.com/new-session");
        })

        this.queue(3000, () => {
            this.$browserWindows[0].find(".page-new-session").css({ display: 'flex'});
        })

        this.queue(1500, () => {
            this.$browserWindows[0].find(".show-command").addClass("pulse animated")
        })

        this.queue(600, () => {
            this.$browserWindows[0].find(".code").html(this.commandText.replace(/\n/g, "<br>"));
            $('.new-session-created').show();
            this.$browserWindows[0].find(".show-command").hide();

            for (i = 0; i <= this.commandText.length; i++) {
                setTimeout((i) => {
                    let beg = this.commandText.substr(0, i);
                    let end = this.commandText.substr(i);
                    this.$browserWindows[0].find(".code").html("<span class='selected'>" + beg.replace(/\n/g, "<br>") + "</span>" + end.replace(/\n/g, "<br>"));
                }, 1300 + 5*i, [i])
            }
        })


        this.queue(4500, () => {
            this.hideWindow('browser', 0);
            this.hideWindow('chat', 0);
        });

        this.queue(500, () => {
            this.openConsoleWindow();
        })

        this.queue(1000, () => {
            this.pushToTerm({ strings: [ this.commandText ], delay: 10 })
        })

        this.queue(300, () => {
            this.setBrowserUrl(0, "https://www.terminalmirror.com/session/e529eb1eade0b214c4", 0);
        })

        this.queue(500, () => {
            this.openWindow('browser', 0, 'Web Browser');
        })


        this.queue(500, () => {
            this.$browserWindows[0].find(".page-console").show();
            this.$browserWindows[0].find(".page-new-session").hide();
        })
    }

    start(tutorWindow, chat1Window, chat2Window, browser1Window, browser2Window, options = {}) {
        this.initDom($(tutorWindow), $(chat1Window), $(chat2Window), $(browser1Window), $(browser2Window));

        this.setupConnection();

        return;
        this.openConsoleWindow();
        let prompt = (delay = 100) => {
            this.queue(delay, () => {
                this.pushToTerm({ strings: [ '<br><span class="text-success">root</span>@main-serv /home/admin# ' ]})
            })
        };

        this.queue(500, () => {
            this.pushToTerm({ strings: [ 'login as: ' ]})
        })

        this.queue(200, () => {
            this.pushToTerm({ strings: ['admin'], typeSpeed: 50, startDelay: 1000});
        })

        this.queue(1800, () => {
            this.pushToTerm({ strings: [ '<br>admin@main-serv\'s password: ' ]})
        })
        this.queue(2000, () => {});

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
            this.queue(10, () => {
                this.pushToTerm({ strings: [ "<br>" + line ]})
            })
        })


        this.queue(500, () => {
            this.pushToTerm({ strings: [ '<br><span class="text-success">admin</span>@main-serv ~$ ' ]})
        })

        this.queue(1500, () => {
            this.pushToTerm({ dealy: 100, strings: ['sudo su -'], typeSpeed: 50});
        });

        prompt(900);

        this.queue(1000, () => {
            this.pushToTerm({ dealy: 100, strings: ['curl localhost'], typeSpeed: 50});
        })


        this.queue(1500, () => {
            this.pushToTerm({ strings: ['<br><span class="text-danger">curl: (7) Failed to connect to localhost port 80: Connection refused<span>']});
        })

        prompt();

        this.queue(500, () => {
            this.pushToTerm({ dealy: 100, strings: ['ps aux | grep apache'], typeSpeed: 50});
        })

        this.queue(1800, () => {
            this.pushToTerm({ strings: [ '<br>root    12547  0.0  0.0  11460   972 pts/9    S+   00:12   0:00 grep --color=auto <span class="text-danger">apache</span>' ]})
        })

        prompt(100);

        this.queue(1500, () => {
            this.pushToTerm({ dealy: 500, strings: ['systemctl start apache2.service'], typeSpeed: 60});
        })

        this.queue(3000, () => {
            this.pushToTerm({ strings: [ '<br><span class="text-danger">Failed to connect to bus: No such file or directory</span>' ]})
        })

        prompt();
        this.queue(500, () => {
            this.pushToTerm({ dealy: 500, strings: ['service apache2 start'], typeSpeed: 60});
        })

        this.queue(2000, () => {
            this.pushToTerm({ strings: [ `<br><span class="text-danger"> * Starting Apache httpd web server apache2<br>
AH00558: apache2: Could not reliably determine the server's fully qualified domain name, using 172.25.0.7. Set the 'ServerName' directive globally to suppress this message<br>
(13)Permission denied: AH00072: make_sock: could not bind to address 0.0.0.0:80<br>
no listening sockets available, shutting down<br>
AH00015: Unable to open logs<br>
Action 'start' failed.<br>
The Apache error log may have more information.<br>
 *
</span>` ]})
        })

        prompt();

        let details = {
            needingHelp: {
                name: "Olaf",
                chatTitle: "Chat (Joe the IT guru)",
            },
            guru: {
                name: "Joe",
                chatTitle: "Olaf"
            }
        }

        this.queue(5000, () => {
            this.openWindow('chat', 0, details.needingHelp.chatTitle);
        })

        this.queue(1000, () => {
            this.sendChatMessage(0, details.needingHelp.name, `Hi ${details.guru.name}, we're having problems with our website. HTTP server is not starting and I'm out of ideas why.`);
        })

        this.queue(5000, () => {
            this.openWindow('chat', 1, details.guru.chatTitle);
        })

        this.queue(3000, () => {
            this.sendChatMessage(1, details.guru.name, "Did you check the logs?");
        });

        this.queue(5000, () => {
            this.sendChatMessage(0, details.needingHelp.name, "Of course I did");
        })

        this.queue(2500, () => {
            this.sendChatMessage(1, details.guru.name, "Can you share your terminal?");
        });

        this.queue(2000, () => {});
        this.setupConnection();

    }
}

module.exports = Tutorial;
