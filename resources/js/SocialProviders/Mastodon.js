import CountTextCharacters from "../Util/CountTextCharacters";

const getPostLength = (content) => {
    // Exclude mastodon server name from the content
    // Example: "@username@server.social", should be replaced by "@username"
    const processedContent = content.replace(/(@[^@\s]+)@[\w.-]+/g, '$1');

    return CountTextCharacters.getLength(processedContent, {
        urlWeight: 23,
        emojiWeight: 1,
    });
};

export default {
    getPostLength
}
