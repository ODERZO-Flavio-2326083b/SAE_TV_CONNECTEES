(() => {
    document.addEventListener('DOMContentLoaded', () => {
        window.OneSignal = window.OneSignal || [];

        const notificationButton = document.getElementById('my-notification-button');
        const wpnonce = document.getElementById('wpnonce').value;
        let subscribing = false;

        const getSubscriptionState = () => {
            return Promise.all([
                OneSignal.isPushNotificationsEnabled(),
                OneSignal.isOptedOut()
            ]).then(function(result) {
                let isPushEnabled = result[0];
                let isOptedOut = result[1];

                return {
                    isPushEnabled: isPushEnabled,
                    isOptedOut: isOptedOut
                };
            });
        }

        const updateButtonAppearance = () => {
            if (!OneSignal.isPushNotificationsSupported()) {
                notificationButton.style.display = 'none';
            } else {
                getSubscriptionState()
                    .then((state) => {
                        const buttonText = !state.isPushEnabled || state.isOptedOut ?
                            'Recevoir des notifications' :
                            'Ne plus recevoir des notifications';

                        notificationButton.innerText = buttonText;
                    });
            }
        }

        const toggleNotificationSubscription = () => {
            getSubscriptionState()
                .then(function(state) {
                    if (state.isPushEnabled) {
                        /* Subscribed, opt them out */
                        return OneSignal.setSubscription(false);
                    } else {
                        if (state.isOptedOut) {
                            /* Opted out, opt them back in */
                            return OneSignal.setSubscription(true);
                        } else {
                            /* Unsubscribed, subscribe them */
                            return OneSignal.registerForPushNotifications();
                        }
                    }
                })
                .then(() => {
                    updateButtonAppearance();
                });
        }

        const updateOneSignalTags = () => {
            const codeTagPrefix = 'code:';
            let adeCodes = [];

            fetch('/wp-json/amu-ecran-connectee/v1/profile/?_wpnonce=' + wpnonce)
                .then((res) => {
                    return res.json();
                })
                .then((json) => {
                    adeCodes = json.codes.map((elem) => elem.code);
                    return OneSignal.getTags();
                })
                .then((tags) => {
                    const codesInTag = [];
                    for (const key of Object.keys(tags)) {
                        if (key.startsWith(codeTagPrefix)) {
                            codesInTag.push(key.substring(codeTagPrefix.length));
                        }
                    }

                    const codesIntersection = codesInTag.filter((elem) => adeCodes.includes(elem));
                    const codesToAdd = adeCodes.filter((elem) => !codesIntersection.includes(elem));
                    const codesToRemove = codesInTag.filter((elem) => !adeCodes.includes(elem));

                    const promises = [];

                    if (codesToAdd.length > 0) {
                        const tagsToAdd = {};
                        for (const code of codesToAdd) {
                            tagsToAdd[codeTagPrefix + code] = 'y';
                        }

                        promises.push(OneSignal.sendTags(tagsToAdd));
                    }

                    if (codesToRemove.length > 0) {
                        const tagsToRemove = codesToRemove.map((elem) => codeTagPrefix + elem);

                        promises.push(OneSignal.deleteTags(tagsToRemove));
                    }

                    return Promise.all(promises);
                });
        }

        OneSignal.push(function() {
            OneSignal.SERVICE_WORKER_PARAM = {scope: '/push/onesignal/'};
            OneSignal.SERVICE_WORKER_PATH = 'wp-content/plugins/plugin-ecran-connecte/public/js/vendor/OneSignalSDKWorker.js'
            OneSignal.SERVICE_WORKER_UPDATER_PATH = 'wp-content/plugins/plugin-ecran-connecte/public/js/vendor/OneSignalSDKUpdaterWorker.js'

            OneSignal.init({
                appId: ONESIGNAL_APP_ID,
            });

            OneSignal.on('subscriptionChange', (isSubscribed) => {
                if (subscribing && isSubscribed) {
                    subscribing = false;
                    toggleNotificationSubscription();
                }
            });

            // Set onesignal tags depending on the ADE codes registered for the user
            updateOneSignalTags();

            updateButtonAppearance();
        });

        notificationButton.addEventListener('click', () => {
            OneSignal.push(function() {
                OneSignal.getNotificationPermission()
                    .then((res) => {
                        if (res === 'default') {
                            OneSignal.showNativePrompt();
                            subscribing = true;
                        } else if (res === 'granted') {
                            toggleNotificationSubscription();
                        } else if (res === 'denied') {
                            alert('Vous avez désactivé les notifications de votre navigateur');
                        }
                    });
            });
        });
    });
})();