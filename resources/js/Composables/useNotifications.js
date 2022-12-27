import emitter from "@/Services/emitter";

const useNotifications = () => {
    const notify = (variant, message, button) => {
        if (typeof message !== 'object') {
            emitter.emit('notify', {variant, message, button});
        }

        if (typeof message === 'object') {
            // Convert laravel validation errors to a string
            const text = Object.keys(message).map((item) => message[item].join("\n")).join("\n");

            emitter.emit('notify', {variant, message: text, button});
        }
    }

    return {
        notify,
    }
}

export default useNotifications;
