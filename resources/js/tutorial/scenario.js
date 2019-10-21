class Scenario {
    constructor(tutorialComponent, chatDetails = {}) {
        this.chatDetails = chatDetails;
        this.tutorialComponent = tutorialComponent;
        this.steps = [];
        this.isRunning = false;

        this.bash = "curl --request POST --include --header \\\n'Content-Type: application/json'\\\n--data-binary @- --no-buffer \\\nhttp://lukas.localdev/LukaszTlalka/terminal-helper/check/test.php";
        this.newSessionUrl = 'https://www.consoleshare.com/new-session';
        this.newSessionStartedUrl = 'https://www.consoleshare.com/session/30D845F2E6DB8E78';

        this.loadSteps();
    }

    genericSteps() {
        let noobBrowserWindow = this.tutorialComponent.display.noob.browserWindow;
        let terminalWindow = this.tutorialComponent.display.noob.terminalWindow;
        let noobChatWindow = this.tutorialComponent.display.noob.chatWindow;
        let guruChatWindow = this.tutorialComponent.display.guru.chatWindow;
        let guruBrowserWindow = this.tutorialComponent.display.guru.browserWindow;

        this.addStep(500, () => {
            noobChatWindow.active = false;
            noobBrowserWindow.show = true;
            noobBrowserWindow.active = true;
            return Promise.resolve();
        });

        this.addStep(500, () => {
            return noobBrowserWindow.setBrowserUrl(this.newSessionUrl)
        })
        this.addStep(10, () => {
            noobBrowserWindow.browser.showNewSession = true;
            noobBrowserWindow.browser.showButton = true;
            return Promise.resolve();
        })

        this.addStep(1000, () => {
            noobBrowserWindow.browser.runButtonAnimation = true;
            return Promise.resolve();
        })

        this.addStep(1000, () => {
            return new Promise((resolve) => {
                noobBrowserWindow.browser.showNewSessionCreated = true;
                noobBrowserWindow.browser.showButton = false;

                noobBrowserWindow.browser.bashBeg = "";
                noobBrowserWindow.browser.bashEnd = this.bash;

                for (i = 0; i <= this.bash.length; i++) {
                    setTimeout((i) => {
                        let beg = this.bash.substr(0, i);
                        let end = this.bash.substr(i);

                        noobBrowserWindow.browser.bashBeg = beg.replace(/\n/g, "<br>");
                        noobBrowserWindow.browser.bashEnd = end.replace(/\n/g, "<br>");

                        if (i == this.bash.length) {
                            resolve();
                        }
                    }, 1300 + 5*i, [i])
                }
            })
        })

        this.addStep(1500, () => {
            noobBrowserWindow.active = false;
            noobChatWindow.active = false;
            terminalWindow.active = true;
            return Promise.resolve();
        })

        this.addStep(1500, () => {
            return terminalWindow.pushToTerm({ dealy: 100, strings: [this.bash]});
        })

        this.addStep(1500, () => {
            terminalWindow.active = false;
            noobBrowserWindow.active = true;
            noobBrowserWindow.browser.showNewSession = true;
            noobBrowserWindow.browser.showNewSessionCreated = true;
            noobBrowserWindow.browser.bashBeg = "";
            noobBrowserWindow.browser.bashEnd = this.bash;

            return Promise.resolve();
        })

        this.addStep(10, () => {
            return noobBrowserWindow.setBrowserUrl(this.newSessionUrl, 0);
        })

        this.addStep(1000, () => {
            noobBrowserWindow.browser.showSpinner = true;
            return Promise.resolve();
        })

        this.addStep(2500, () => {
            noobBrowserWindow.browser.showNewSession = false;
            noobBrowserWindow.browser.showTerminal = true;
            return noobBrowserWindow.setBrowserUrl(this.newSessionStartedUrl, 0);
        })

        this.addStep(20, () => {
            return noobBrowserWindow.pushToTerm({ strings: [ '>_ $' ]});
        })

        this.addStep(2500, () => {
            noobBrowserWindow.class += ' selected-address';
            return noobBrowserWindow.setBrowserUrl(this.newSessionStartedUrl, 0);
        });

        this.addStep(1000, () => {
            noobBrowserWindow.active = false;
            noobChatWindow.show = true;
            noobChatWindow.active = true;

            return Promise.resolve();
        });

        this.addStep(1000, () => {
            return noobChatWindow.sendChatMessage(this.chatDetails.noob.name, "Here it is:", guruChatWindow, 0);
        });

        this.addStep(300, () => {
            return noobChatWindow.sendChatMessage(this.chatDetails.noob.name, this.newSessionStartedUrl, guruChatWindow, 0);
        });

        this.addStep(1000, () => {
            noobBrowserWindow.active = true;
            noobChatWindow.active = false;

            return Promise.resolve();
        });

        this.addStep(500, () => {
            noobBrowserWindow.class = noobBrowserWindow.class.replace(' selected-address', '')

            guruChatWindow.active = false;
            guruBrowserWindow.show = true;
            guruBrowserWindow.active = true;

            return Promise.resolve();
        });

        this.addStep(1300, () => {
            guruBrowserWindow.browser.showTerminal = true;
            return guruBrowserWindow.setBrowserUrl(this.newSessionStartedUrl, 0);
        });

        this.addStep(10, () => {
            return guruBrowserWindow.pushToTerm({ strings: [ '>_ $' ]});
        })
    }

    addStep(delay, closure) {
        this.steps.push({
            delay,
            closure
        });
    }

    startTutorial() {
        this.currentStep = 0;

        this.runStep();
    }

    runStep() {
        if (!this.steps[ this.currentStep ])
            return;

        setTimeout(() => {
            this.steps[ this.currentStep ].closure().then( () => {
                this.currentStep++;
                this.runStep();
            } )
        },this.steps[ this.currentStep ].delay);
    }
}

module.exports = Scenario
