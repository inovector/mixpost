import emitter from "@/Services/emitter";

const useNotifications = () => {
    const notify = (variant, message) => {
        emitter.emit('notify', {variant, message});
    }

    return {
        notify,
    }
}

export default useNotifications;
