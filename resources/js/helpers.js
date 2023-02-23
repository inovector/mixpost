import {isProxy, toRaw} from "vue";
import {utcToZonedTime} from "date-fns-tz";
import {format} from "date-fns";

export function getWindowDimensions() {
    let width = Math.max(
        document.body.scrollWidth,
        document.documentElement.scrollWidth,
        document.body.offsetWidth,
        document.documentElement.offsetWidth,
        document.documentElement.clientWidth
    );

    let height = Math.max(
        document.body.scrollHeight,
        document.documentElement.scrollHeight,
        document.body.offsetHeight,
        document.documentElement.offsetHeight,
        document.documentElement.clientHeight
    );

    return {width, height};
}

export function lightOrDark(color) {
    // Variables for red, green, blue values
    let r, g, b, hsp;

    // Check the format of the color, HEX or RGB?
    if (color.match(/^rgb/)) {
        // If RGB --> store the red, green, blue values in separate variables
        color = color.match(/^rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*(\d+(?:\.\d+)?))?\)$/);

        r = color[1];
        g = color[2];
        b = color[3];
    } else {
        // If hex --> Convert it to RGB: http://gist.github.com/983661
        color = +("0x" + color.slice(1).replace(
            color.length < 5 && /./g, '$&$&'));

        r = color >> 16;
        g = color >> 8 & 255;
        b = color & 255;
    }

    // HSP (Highly Sensitive Poo) equation from http://alienryderflex.com/hsp.html
    hsp = Math.sqrt(
        0.299 * (r * r) +
        0.587 * (g * g) +
        0.114 * (b * b)
    );

    // Using the HSP value, determine whether the color is light or dark
    if (hsp > 127.5) {
        return 'light';
    }

    return 'dark';
}

export function decomposeString(string) {
    return string.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
}

export function isTimePast(date, timeZone = null) {
    const today = timeZone ? utcToZonedTime(new Date().toISOString(), timeZone) : new Date();

    return date.getTime() < today.getTime()
}

export function isDatePast(date, timeZone = null) {
    const today = timeZone ? utcToZonedTime(new Date().toISOString(), timeZone) : new Date();

    today.setHours(0, 0, 0, 0);

    return date < today;
}

export function isDateTimePast(datetime, timeZone = null) {
    const today = timeZone ? utcToZonedTime(new Date().toISOString(), timeZone) : new Date();
    today.setSeconds(0);

    return datetime < today;
}

export function convertTime12to24(time12h) {
    const [time, modifier] = time12h.split(' ');

    let [hours, minutes] = time.split(':');

    if (hours === '12') {
        hours = '00';
    }

    if (modifier === 'PM') {
        hours = parseInt(hours, 10) + 12;
    }

    return `${hours}:${minutes}`;
}

export function convertTime24to12(time24h, customFormat = 'h:mmaaa') {
    const date = new Date();

    const [hours, minutes] = time24h.split(':');

    date.setHours(hours, minutes);

    return format(date, customFormat);
}

export function toRawIfProxy(obj) {
    return isProxy(obj) ? toRaw(obj) : obj
}
