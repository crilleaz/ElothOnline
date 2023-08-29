/**
 * @copyright https://github.com/TemirkhanN/lpn
 *
 * @param message text that will be shown in popup. Popup won't be shown if empty or null value passed
 * @param messageType color of popup will be generated based on type.
 * Success is green popup; Notice is blue; Warning is yellow; Error is red;
 * Default: notice;
 *
 * @param position html based position of popup. Default: 'bottom left'
 * @param duration time in seconds before popup disappear. default is 5 seconds
 */
function PopupMessage(message, messageType, position, duration) {
    /**
     * Available message types
     *
     * @type {*}
     */
    var messageTypes = {
        success: 'success',
        notice: 'notice',
        warning: 'warning',
        error: 'error'
    };

    /**
     * Elements DOM parameters such as style properties, class name and etc.
     *
     * @type {*}
     */
    var elements = {
        popupWindow: {
            className: 'lpn-window',
            style: {
                fontSize: '14px',
                position: 'fixed',
                zIndex: '100000',
                padding: '20px',
                marginBottom: '20px;',
                border: '1px solid transparent',
                borderRadius: '4px'
            }
        },
        hideButton: {
            className: 'close-popup',
            innerHTML: '×',
            title: 'Hide',
            style: {
                position: 'absolute',
                top: '0',
                right: '5px',
                fontSize: '18px',
                fontWeight: 'bold',
                cursor: 'pointer'
            }
        }
    };

    /**
     * Constructor
     */
    var popupMessage = function (message, type, position, duration) {
        var popupAttributes       = elements.popupWindow;
        popupAttributes.innerHTML = message;

        var popup = fillAttributes(document.createElement('div'), popupAttributes);

        setId(popup);
        setPosition(popup, position);
        applyMessageType(popup, type);
        attachHideButton(popup);

        document.body.appendChild(popup);

        //Destroy popup after number of seconds
        setTimeout(function () {
            removePopup(popup);
        }, duration * 1000);
    };

    /**
     * Appends closing button to popup window
     *
     * @param popup
     */
    var attachHideButton = function (popup) {
        if (!popup.id) {
            return;
        }

        var hideButton = fillAttributes(document.createElement('span'), elements.hideButton);

        hideButton.addEventListener('click', function () {
            removePopup(popup);
        });

        popup.appendChild(hideButton);
    };

    /**
     * Applies visual changes to popup based on message type
     *
     * @param popup
     * @param messageType
     */
    var applyMessageType = function (popup, messageType) {
        switch (messageType) {
            case messageTypes.success:
                fillAttributes(popup, {
                    style: {
                        color: '#3C763D',
                        backgroundColor: '#DFF0D8',
                        borderColor: '#D6E9C6'
                    }
                });
                break;
            case messageTypes.notice:
                fillAttributes(popup, {
                    style: {
                        color: '#31708F',
                        backgroundColor: '#D9EDF7',
                        borderColor: '#BCE8F1'
                    }
                });
                break;
            case messageTypes.warning:
                fillAttributes(popup, {
                    style: {
                        color: '#8A6D3B',
                        backgroundColor: '#FCF8E3',
                        borderColor: '#FAEBCC'
                    }
                });
                break;
            case messageTypes.error:
                fillAttributes(popup, {
                    style: {
                        color: '#A94442',
                        backgroundColor: '#F2DEDE',
                        borderColor: '#EBCCD1'
                    }
                });
                break;
        }
    };

    /**
     * Gets unique identifier for popup.
     *
     * @param popup
     */
    var setId = function (popup) {

        this.popupId = typeof this.popupId == 'undefined' ? 1 : this.popupId + 1;

        popup.id = 'lpn-message' + this.popupId;
    };

    /**
     * Sets popup at position based on passed params. By default it will be placed in bottom left corner
     *
     * @param popup
     * @param position
     */
    var setPosition = function (popup, position) {
        var positionInfo = position.split(' ');
        if (positionInfo.length == 2) {
            positionInfo[0] == 'top' ? popup.style.top = '20px' : popup.style.bottom = '20px';
            positionInfo[1] == 'right' ? popup.style.right = '20px' : popup.style.left = '20px';
        }
    };

    /**
     * Removes popup from DOM tree
     *
     * @param popup
     */
    var removePopup = function (popup) {
        if (document.getElementById(popup.id)) {
            document.body.removeChild(popup);
        }
    };

    /**
     * Recursively declares DOM element attributes based on passed values
     *
     * @param targetElement
     * @param attributes
     * @returns {*}
     */
    var fillAttributes = function (targetElement, attributes) {
        for (var i in attributes) {
            if (attributes.hasOwnProperty(i)) {
                if (typeof attributes[i] == "object") {
                    fillAttributes(targetElement[i], attributes[i]);
                } else {
                    targetElement[i] = attributes[i];
                }
            }
        }

        return targetElement;
    };

    popupMessage(
        message,
        typeof messageType == 'undefined' || !messageType.toLowerCase() in messageTypes ? messageTypes.notice : messageType.toLowerCase(),
        typeof position == 'undefined' || position.length == 0 ? 'bottom left' : position,
        typeof duration == 'undefined' ? 5 : parseInt(duration)
    );
}
