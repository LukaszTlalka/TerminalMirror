const EventClass = require("event-class").default;

class Scenario{
    constructor(tutorialComponent, chatDetails = {}) {
        this.chatDetails = chatDetails;
        this.tutorialComponent = tutorialComponent;
        this.steps = [];

        /*
         * Control step scrolling
         */
        this.jumpToStepNum = false;
        this.stepRunning = false;
        this.stepTimeout = false;
        this.forceStop = false;
        this.tutorialDisplay = JSON.stringify(this.tutorialComponent.display);

        this.event = new EventClass();

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


                if (this.speedRun) {
                    noobBrowserWindow.browser.bashBeg = this.bash;
                    noobBrowserWindow.browser.bashEnd = "";
                    resolve();
                    return;
                }

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
            return noobChatWindow.sendChatMessage(this.chatDetails.noob.name, "Here it is:", guruChatWindow);
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

    recursiveCopyDisplay(copyTo, copyFrom) {
        Object.keys(copyFrom).map(key => {
            if (copyFrom[key].constructor.name === "Object") {
                this.recursiveCopyDisplay(copyTo[key], copyFrom[key]);
            } else {
                copyTo[key] = copyFrom[key];
            }
        });
    }

    jumpToStep(stepID) {
        this.jumpToStepNum = stepID;
        this.forceStop = true;

        let runningChkInterval = setInterval(() => {
            clearTimeout(this.stepTimeout);
            if (this.stepRunning) {
                return;
            }

            clearInterval(runningChkInterval);

            if (stepID < this.currentStep) {
                this.recursiveCopyDisplay(this.tutorialComponent.display, JSON.parse(this.tutorialDisplay));

                this.currentStep = 0;
                this.tutorialComponent.enabled = false;
                this.tutorialComponent.$forceUpdate();

                setTimeout(() => {
                    this.tutorialComponent.enabled = true;
                    this.tutorialComponent.display.mainMessage.show = false;
                    this.tutorialComponent.speedRun = true;

                    this.speedRun = true;
                    this.forceStop = false;

                    this.runStep();
                }, 100);

                return;
            }

            this.tutorialComponent.speedRun = true;

            this.speedRun = true;
            this.forceStop = false;

            this.runStep();
        }, 10)
    }

    runStep() {
        this.tutorialComponent.$forceUpdate();


        if (this.forceStop || !this.steps[ this.currentStep ])
            return;

        this.stepTimeout = setTimeout(() => {
            this.stepRunning = true;
            this.event.trigger("steps:change", this.currentStep);
            this.steps[ this.currentStep ].closure(this.speedRun).then( () => {
                this.stepRunning = false;
                this.currentStep++;

                this.runStep();

                if (this.jumpToStepNum !== false && this.currentStep >= this.jumpToStepNum) {
                    this.jumpToStepNum = false;
                    this.tutorialComponent.speedRun = false;
                    this.speedRun = false;
                }
            } )
        }, this.speedRun ? 0 : this.steps[ this.currentStep ].delay);
    }
}

module.exports = Scenario
