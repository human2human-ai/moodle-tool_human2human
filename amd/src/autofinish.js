// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Finishes the setup once pairing is done, and shows what to do if it cannot tell.
 *
 * Two signals, because neither is guaranteed: Human2Human's
 * org.imsglobal.lti.close message, which only arrives if the pairing tab kept
 * its opener, and the administrator coming back to this tab. Either one posts
 * the page's own sesskey-checked finishsetup action, which does nothing until a
 * tool is registered. So any sender may trigger it, and an early return only
 * reloads the page, once: the flag is cleared before posting.
 *
 * @module     tool_human2human/autofinish
 * @copyright  2026 eduNEXT {@link https://www.edunext.co}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Config from 'core/config';

const STORAGE_KEY = 'tool_human2human/pairingstarted';

// Long enough to sign up on Human2Human first, short enough that a later visit
// to this tab does not reload it.
const PAIRING_WINDOW_MS = 30 * 60 * 1000;

const markStarted = () => {
    try {
        window.sessionStorage.setItem(STORAGE_KEY, String(Date.now()));
    } catch (error) {
        // Storage can be blocked; the close message still works without it.
    }
};

const isPairing = () => {
    try {
        const startedAt = Number(window.sessionStorage.getItem(STORAGE_KEY));
        return startedAt > 0 && Date.now() - startedAt < PAIRING_WINDOW_MS;
    } catch (error) {
        return false;
    }
};

const clearStarted = () => {
    try {
        window.sessionStorage.removeItem(STORAGE_KEY);
    } catch (error) {
        // Nothing was stored then.
    }
};

const finish = (pageUrl) => {
    // Post once per Pair press. An early return comes back with the waiting hint
    // shown by the server, and returning to the tab again must not reload it, nor
    // must a later unpair in this tab find the flag still set.
    clearStarted();
    const form = document.createElement('form');
    form.method = 'post';
    form.action = pageUrl;
    Object.entries({action: 'finishsetup', auto: '1', sesskey: Config.sesskey}).forEach(([name, value]) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        form.appendChild(input);
    });
    document.body.appendChild(form);
    form.submit();
};

/**
 * Watch for the end of pairing.
 *
 * @param {string} pageUrl The Human2Human admin page.
 * @param {string} formId The Pair button's form, which opens the pairing tab.
 * @param {string} waitingId What to show while pairing: a hint and a check button.
 */
export const init = (pageUrl, formId, waitingId) => {
    const waiting = document.getElementById(waitingId);
    const showWaiting = () => {
        if (waiting) {
            waiting.hidden = false;
        }
    };
    // Still pairing after an early return reloaded the page.
    if (isPairing()) {
        showWaiting();
    }

    const form = document.getElementById(formId);
    if (form) {
        // The form carries rel="opener" in its markup: a form submitted to a new
        // tab implies noopener, like a link, which would leave the tool no window
        // to send its close message to. The tool is the one this site is pairing
        // with, which Dynamic Registration trusts anyway.
        form.addEventListener('submit', () => {
            markStarted();
            showWaiting();
        });
    }

    window.addEventListener('message', (event) => {
        if (event.data?.subject === 'org.imsglobal.lti.close') {
            finish(pageUrl);
        }
    });

    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'visible' && isPairing()) {
            finish(pageUrl);
        }
    });
};
