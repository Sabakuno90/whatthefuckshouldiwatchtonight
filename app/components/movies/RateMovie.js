import React, { Component, PropTypes } from 'react'
import { browserHistory } from 'react-router'
import Alert from 'react-s-alert'
import request from 'superagent'
import isEmpty from 'lodash/isempty'
import { SelectableEmotion } from '../emotions/Emotion'
import { apiKey } from '../../settings.js'
import { parseReleaseYear, handleRequest } from '../../utils.js'

/**
 * This component presents the user with the option to select an emotion and
 * submit it along with a movie, i.e. create a review.
 */

class RateMovie extends Component {
    static propTypes = {
        movieId: PropTypes.number.isRequired,
        addCallback: PropTypes.func,
        errorCallback: PropTypes.func
    }

    constructor(props) {
        super(props)

        this.state = {
            movie: {},
            emotions: []
        }

        this.getEmotions()
        this.getMovie(this.props.movieId)

        this.saveRating = this.saveRating.bind(this)
    }

    /**
     * Retrieve the information for a given movie.
     *
     * @param   {integer}  movieId  The ID of the movie to retrieve information
     *                              for.
     *
     * @return  {void}
     */

    getMovie(movieId) {
        this.movieRequest = request.get(`http://api.themoviedb.org/3/movie/${movieId}?api_key=${apiKey}&append_to_response=credits`)
            .set('Accept', 'application/json')
            .end((err, res) => handleRequest(err, res, res => {
                this.setState({
                    movie: res.body
                })

                this.movieRequest = null
            }, () => {
                Alert.error('Could not communicate to the themoviedb.org servers. Please try again.')

                if (this.props.errorCallback) {
                    this.props.errorCallback()
                }

                this.movieRequest = null
            }))
    }

    /**
     * Retrieve the list of emotions the user will be able to select after
     * choosing a movie.
     *
     * @return  {void}
     */

    getEmotions() {
        this.emotionsRequest = request.get('/api/emotions/')
            .set('Accept', 'application/json')
            .end((err, res) => handleRequest(err, res, res => {
                const emotions = res.body.emotions

                // Add a `selected` flag to each emotion.

                emotions.forEach((e, i) => emotions[i].selected = false)

                this.setState({
                    emotions: emotions
                })

                this.emotionsRequest = null
            }, () => {
                Alert.error('Could not retrieve available emotions. Please try again.')

                if (this.props.errorCallback) {
                    this.props.errorCallback()
                }

                this.emotionsRequest = null
            }))
    }

    /**
     * selectedEmotion() marks a single emotion as selected and sets the flag on
     * all other emotions to false.
     *
     * @param   {integer}  index  The index of the emotion inside the state
     *                            array.
     *
     * @return  {void}
     */

    selectedEmotion(index) {
        const emotions = this.state.emotions

        // Completely reset and then set the flag.

        emotions.forEach((e, i) => emotions[i].selected = false)
        emotions[index].selected = true

        this.setState({
            emotions: emotions
        })
    }

    /**
     * saveRating() saves the current movie along with the selected emotion.
     * Once the request is done, the user is redirected to the page of the
     * emotion they've just selected. If an addCallback() is defined, it is
     * called here.
     *
     * @return  {void}
     */

    saveRating() {
        if (this.saveRequest) return

        const selectedEmotion = this.state.emotions.filter(e => e.selected)

        if (isEmpty(selectedEmotion)) {
            Alert.error('Could not save your rating. Please try again.')
            return
        }

        this.saveRequest = request.post('/api/movies/')
            .set('Content-Type', 'application/json')
            .send(JSON.stringify({ ...this.state.movie, emotionId: selectedEmotion[0].id }))
            .end((err, res) => handleRequest(err, res, res => {
                browserHistory.push(`/${selectedEmotion[0].emotion}/`)

                // Call the callback function with the just selected
                // emotion.

                if (this.props.addCallback) {
                    this.props.addCallback(selectedEmotion[0].emotion)
                }

                this.saveRequest = null
            }, () => {
                Alert.error('Could not save your rating. Please try again.')
                this.saveRequest = null
            }))
    }

    /**
     * Cancel all requests before the component is unmounted.
     *
     * @return  {void}
     */

    componentWillUnmount() {
        if (this.movieRequest) {
            this.movieRequest.abort()
            this.movieRequest = null
        }

        if (this.emotionsRequest) {
            this.emotionsRequest.abort()
            this.emotionsRequest = null
        }

        if (this.saveRequest) {
            this.saveRequest.abort()
            this.saveRequest = null
        }
    }

    render() {
        const { movie, emotions } = this.state

        return (
            <section data-grid>
                <section className="clearfix add-movie-information">
                    <div data-col="1-6">
                        <img className="result-poster"
                             src={movie.poster_path ? `https://image.tmdb.org/t/p/w185${movie.poster_path}` : ''} />
                    </div>
                    <div data-col="5-6">
                        <h2 className="h3 detail-title">
                            <span className="highlighted">{movie.title}</span> {parseReleaseYear(movie.release_date)}
                        </h2>
                        {!isEmpty(movie.credits) &&
                            <p>by {movie.credits.crew.filter(c => c.job == "Director").map(c => c.name).join(', ')}</p>
                        }
                    </div>
                </section>
                <ul className="unstyled-list clearfix selectable-emotions-list">
                    {emotions.map((e, i) => (
                        <SelectableEmotion name={e.emotion}
                                           isSelected={e.selected}
                                           key={i}
                                           onClick={() => this.selectedEmotion(i)} />
                    ))}
                </ul>
                <div data-col="1-2">
                    <button data-button="block secondary"
                            onClick={this.props.closeCallback}>
                        Cancel
                    </button>
                </div>
                <div data-col="1-2">
                    <button data-button="block"
                            onClick={this.saveRating}>
                        Save Rating
                    </button>
                </div>
            </section>
        )
    }
}

export default RateMovie
