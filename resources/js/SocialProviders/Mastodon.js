import CountTextCharacters from "../Util/CountTextCharacters";

const getPostLength = (content) => {
    // Exclude mastodon server name from the content
    // Example: "@username@server.social", should be replaced by "@username"
    const lines = content.split('\n');

    const processedLines = lines.map(line => {
        const words = line.split(' ');

        const processedWords = words.map(word => word.replace(/(@[^@\s]+)@.*/, '$1'));

        return processedWords.join(' ');
    });

    let result = processedLines.join('\n');

    return CountTextCharacters.getLength(result, {
        urlWeight: null,
        emojiWeight: 1,
    });
};

export default {
    getPostLength
}
